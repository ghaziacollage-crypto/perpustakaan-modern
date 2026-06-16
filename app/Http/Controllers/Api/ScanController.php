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

        $alreadyAdded = \App\Models\BorrowingDetail::where('book_id', $bookValidation['book']['id'])
            ->where('status', \App\Enums\BorrowingDetailStatus::Borrowed)
            ->whereHas('borrowing', fn ($q) => $q
                ->where('member_id', $member->id)
                ->whereIn('status', [
                    \App\Enums\BorrowingStatus::Pending->value,
                    \App\Enums\BorrowingStatus::Active->value,
                    \App\Enums\BorrowingStatus::Late->value,
                ]))
            ->exists();

        if ($alreadyAdded) {
            return response()->json([
                'success' => false,
                'error' => 'Buku ini sudah ada di daftar pinjam.',
            ], 422);
        }

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

        $pendingBorrowings = \App\Models\Borrowing::where('member_id', $member->id)
            ->where('status', \App\Enums\BorrowingStatus::Pending)
            ->whereDate('created_at', now()->toDateString())
            ->with(['details.book'])
            ->latest()
            ->get();

        $pendingBooks = $pendingBorrowings->flatMap->details;
        $firstPending = $pendingBorrowings->first();

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
            'pending_borrowing' => $firstPending ? [
                'id' => $firstPending->id,
                'transaction_code' => $firstPending->transaction_code,
                'transaction_codes' => $pendingBorrowings->pluck('transaction_code')->values(),
                'books' => $pendingBooks->map(fn ($d) => [
                    'id' => $d->id,
                    'book_id' => $d->book_id,
                    'title' => $d->book->title,
                    'author' => $d->book->author,
                    'book_code' => $d->book->book_code,
                    'cover' => $d->book->cover_url,
                ]),
                'total_books' => $pendingBooks->count(),
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

        $detail = \App\Models\BorrowingDetail::where('book_id', $bookId)
            ->where('status', \App\Enums\BorrowingDetailStatus::Borrowed)
            ->whereHas('borrowing', fn ($q) => $q
                ->where('member_id', $member->id)
                ->where('status', \App\Enums\BorrowingStatus::Pending)
                ->whereDate('created_at', now()->toDateString()))
            ->first();

        if (! $detail) {
            return response()->json([
                'success' => false,
                'error' => 'Buku tidak ditemukan di daftar.',
            ], 404);
        }

        $pendingBorrowing = $detail->borrowing;
        $detail->delete();

        $pendingBorrowing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku dihapus dari daftar.',
            'borrowing' => null,
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
