<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Member;
use App\Models\MemberAttendance;
use App\Services\BorrowingService;
use App\Services\MemberAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ScanController extends Controller
{
    public function __construct(
        private readonly BorrowingService $borrowingService,
        private readonly MemberAttendanceService $attendanceService,
    ) {}

    /**
     * POST /api/scan/member
     * Record member attendance when they scan their QR at the library kiosk
     */
    public function scanMember(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:50'],
        ]);

        $member = Member::where('member_code', $validated['member_code'])->first();

        if (! $member) {
            return response()->json([
                'success' => false,
                'error' => 'Member tidak ditemukan.',
            ], 404);
        }

        if (! $member->isActive()) {
            return response()->json([
                'success' => false,
                'error' => 'Status member tidak aktif. Hubungi admin.',
            ], 403);
        }

        $attendance = $this->attendanceService->recordAttendance($member);

        return response()->json([
            'success' => true,
            'message' => "Hai {$member->name}! Selamat datang di perpustakaan.",
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'member_code' => $member->member_code,
                'nis_nim' => $member->nis_nim,
                'photo' => $member->qr_code_url,
                'class' => $member->class,
                'major' => $member->major,
                'remaining_slots' => $member->remaining_slots,
                'active_borrowings_count' => $member->active_borrowings_count,
            ],
            'attendance_id' => $attendance->id,
        ]);
    }

    /**
     * POST /api/scan/book
     * Add a book to the current pending borrowing session
     * Creates a pending borrowing if none exists for this member today
     */
    public function scanBook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:50'],
            'book_code' => ['required', 'string', 'max:50'],
        ]);

        $member = Member::where('member_code', $validated['member_code'])->first();

        if (! $member) {
            return response()->json([
                'success' => false,
                'error' => 'Member tidak ditemukan.',
            ], 404);
        }

        // Validate and get book info
        $bookValidation = $this->borrowingService->validateBookByCode($validated['book_code']);

        if (! $bookValidation['success']) {
            return response()->json([
                'success' => false,
                'error' => $bookValidation['error'],
            ], 422);
        }

        // Check if member has a pending borrowing already (from today)
        $existingPending = \App\Models\Borrowing::where('member_id', $member->id)
            ->where('status', \App\Enums\BorrowingStatus::Pending)
            ->whereDate('created_at', now()->toDateString())
            ->with('details.book')
            ->first();

        if ($existingPending) {
            // Check if book is already in this pending borrowing
            $alreadyAdded = $existingPending->details->contains('book_id', $bookValidation['book']['id']);
            if ($alreadyAdded) {
                return response()->json([
                    'success' => false,
                    'error' => 'Buku ini sudah ada di daftar pinjam.',
                ], 422);
            }

            // Add book to existing pending borrowing
            \App\Models\BorrowingDetail::create([
                'borrowing_id' => $existingPending->id,
                'book_id' => $bookValidation['book']['id'],
                'status' => \App\Enums\BorrowingDetailStatus::Borrowed,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Buku ditambahkan ke daftar pinjam.',
                'borrowing' => $existingPending->fresh(['member', 'details.book']),
                'book' => $bookValidation['book'],
            ]);
        }

        // Create new pending borrowing with this book
        try {
            $borrowing = $this->borrowingService->createPending(
                $member,
                [$bookValidation['book']['id']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Buku ditambahkan. Ajukan peminjaman saat selesai.',
                'borrowing' => $borrowing,
                'book' => $bookValidation['book'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->errors()['book_ids'][0] ?? 'Gagal menambahkan buku.',
            ], 422);
        }
    }

    /**
     * GET /api/scan/current-member
     * Get current member info by member code (for kiosk polling)
     */
    public function getCurrentMember(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:50'],
        ]);

        $member = Member::where('member_code', $validated['member_code'])->first();

        if (! $member) {
            return response()->json([
                'success' => false,
                'error' => 'Member tidak ditemukan.',
            ], 404);
        }

        // Get pending borrowing if exists
        $pendingBorrowing = \App\Models\Borrowing::where('member_id', $member->id)
            ->where('status', \App\Enums\BorrowingStatus::Pending)
            ->whereDate('created_at', now()->toDateString())
            ->with(['details.book'])
            ->first();

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'member_code' => $member->member_code,
                'photo' => $member->qr_code_url,
                'remaining_slots' => $member->remaining_slots,
                'active_borrowings_count' => $member->active_borrowings_count,
            ],
            'pending_borrowing' => $pendingBorrowing ? [
                'id' => $pendingBorrowing->id,
                'transaction_code' => $pendingBorrowing->transaction_code,
                'books' => $pendingBorrowing->details->map(fn ($d) => [
                    'id' => $d->id,
                    'book_id' => $d->book_id,
                    'title' => $d->book->title,
                    'author' => $d->book->author,
                    'book_code' => $d->book->book_code,
                    'cover' => $d->book->cover_url,
                ]),
                'total_books' => $pendingBorrowing->details->count(),
            ] : null,
        ]);
    }

    /**
     * DELETE /api/scan/book/{bookId}
     * Remove a book from the pending borrowing
     */
    public function removeBook(Request $request, int $bookId): JsonResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:50'],
        ]);

        $member = Member::where('member_code', $validated['member_code'])->first();

        if (! $member) {
            return response()->json([
                'success' => false,
                'error' => 'Member tidak ditemukan.',
            ], 404);
        }

        $pendingBorrowing = \App\Models\Borrowing::where('member_id', $member->id)
            ->where('status', \App\Enums\BorrowingStatus::Pending)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if (! $pendingBorrowing) {
            return response()->json([
                'success' => false,
                'error' => 'Tidak ada peminjaman pending.',
            ], 404);
        }

        $detail = $pendingBorrowing->details()->where('book_id', $bookId)->first();

        if (! $detail) {
            return response()->json([
                'success' => false,
                'error' => 'Buku tidak ditemukan di daftar.',
            ], 404);
        }

        $detail->delete();

        // If no more books, delete the pending borrowing
        if ($pendingBorrowing->details()->count() === 0) {
            $pendingBorrowing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Buku dihapus. Semua buku dibatalkan.',
                'borrowing' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Buku dihapus dari daftar.',
            'borrowing' => $pendingBorrowing->fresh(['member', 'details.book']),
        ]);
    }

    /**
     * GET /api/scan/queue
     * Get list of members currently at the library (for admin dashboard)
     */
    public function getQueue(): JsonResponse
    {
        $members = $this->attendanceService->getCurrentMembers();

        return response()->json([
            'success' => true,
            'members' => $members,
            'count' => count($members),
        ]);
    }
}