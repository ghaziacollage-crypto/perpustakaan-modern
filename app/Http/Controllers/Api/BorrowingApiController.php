<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Borrowings\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BorrowingApiController extends Controller
{
    public function __construct(
        private readonly BorrowingService $borrowingService,
        private readonly ReceiptService $receiptService,
    ) {}

    /**
     * GET /api/members/lookup?code={member_code}
     * Lookup member by QR code and return slot info + active borrowings
     */
    public function lookupMember(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json(['success' => false, 'error' => 'Kode member diperlukan.'], 400);
        }

        $result = $this->borrowingService->validateMemberByCode($code);

        if (! $result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * GET /api/books/lookup?code={book_code}
     * Lookup book by QR code with validation
     */
    public function lookupBook(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json(['success' => false, 'error' => 'Kode buku diperlukan.'], 400);
        }

        $result = $this->borrowingService->validateBookByCode($code);

        if (! $result['success']) {
            return response()->json($result, $result['error'] === 'Buku tidak ditemukan.' ? 404 : 409);
        }

        return response()->json($result);
    }

    /**
     * POST /api/borrowings
     * Create new borrowing
     */
    public function store(StoreBorrowingRequest $request): JsonResponse
    {
        try {
            $member = Member::findOrFail($request->integer('member_id'));
            $dueDate = $request->has('due_date')
                ? Carbon::parse($request->input('due_date'))
                : null;

            $borrowing = $this->borrowingService->createBorrowing(
                $member,
                $request->input('book_ids', []),
                $dueDate,
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil disimpan.',
                'data' => [
                    'borrowing_id' => $borrowing->id,
                    'transaction_code' => $borrowing->transaction_code,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/borrowings/{id}
     * Get borrowing details
     */
    public function show(Borrowing $borrowing): JsonResponse
    {
        $borrowing->load(['member', 'details.book', 'fine']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $borrowing->id,
                'transaction_code' => $borrowing->transaction_code,
                'loan_date' => $borrowing->loan_date->format('d M Y'),
                'due_date' => $borrowing->due_date->format('d M Y'),
                'return_date' => $borrowing->return_date?->format('d M Y'),
                'status' => $borrowing->status->value,
                'status_label' => $this->getStatusLabel($borrowing),
                'is_overdue' => $borrowing->isOverdue(),
                'days_overdue' => $borrowing->daysOverdue(),
                'member' => [
                    'id' => $borrowing->member->id,
                    'name' => $borrowing->member->name,
                    'member_code' => $borrowing->member->member_code,
                    'nis_nim' => $borrowing->member->nis_nim,
                    'photo' => $borrowing->member->photo ? asset('storage/'.$borrowing->member->photo) : null,
                ],
                'books' => $borrowing->details->map(fn ($d) => [
                    'id' => $d->id,
                    'title' => $d->book->title,
                    'book_code' => $d->book->book_code,
                    'author' => $d->book->author,
                    'cover' => $d->book->cover_url,
                    'status' => $d->status->value,
                    'returned_at' => $d->returned_at?->format('d M Y'),
                ]),
                'fine' => $borrowing->fine ? [
                    'amount' => number_format($borrowing->fine->total_amount),
                    'status' => $borrowing->fine->status->value,
                ] : null,
            ],
        ]);
    }

    /**
     * GET /api/borrowings/{id}/receipt
     * Get receipt data
     */
    public function receipt(Borrowing $borrowing): JsonResponse
    {
        $data = $this->receiptService->generateReceiptData($borrowing);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * GET /api/borrowings/{id}/receipt/pdf
     * Download receipt PDF
     */
    public function receiptPdf(Borrowing $borrowing): \Illuminate\Http\Response
    {
        return $this->receiptService->downloadPdf($borrowing);
    }

    /**
     * GET /api/settings/borrowing
     * Get borrowing-related settings
     */
    public function settings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'loan_duration_days' => $this->borrowingService->getLoanDuration(),
                'default_due_date' => $this->borrowingService->getDefaultDueDate()->toDateString(),
                'max_borrowings_per_member' => Member::MAX_BORROWINGS,
            ],
        ]);
    }

    /**
     * GET /api/borrowings/by-code?code={transaction_code}
     * Lookup borrowing by transaction_code (no auth required — used by return scan)
     */
    public function lookupByTransactionCode(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json(['success' => false, 'error' => 'Kode transaksi diperlukan.'], 400);
        }

        $borrowing = \App\Models\Borrowing::with(['member', 'details.book'])
            ->where('transaction_code', $code)
            ->first();

        if (! $borrowing) {
            return response()->json(['success' => false, 'error' => 'Peminjaman tidak ditemukan.'], 404);
        }

        // Only show borrowings that still have unreturned books
        $hasUnreturned = $borrowing->details()->where('status', 'borrowed')->exists();
        if (! $hasUnreturned) {
            return response()->json([
                'success' => false,
                'error' => 'Semua buku pada peminjaman ini sudah dikembalikan.',
            ], 410);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $borrowing->id,
                'transaction_code' => $borrowing->transaction_code,
                'loan_date' => $borrowing->loan_date->format('d M Y'),
                'due_date' => $borrowing->due_date->format('d M Y'),
                'return_date' => $borrowing->return_date?->format('d M Y'),
                'status' => $borrowing->status->value,
                'is_overdue' => $borrowing->isOverdue(),
                'days_overdue' => $borrowing->daysOverdue(),
                'member' => [
                    'id' => $borrowing->member->id,
                    'name' => $borrowing->member->name,
                    'member_code' => $borrowing->member->member_code,
                    'nis_nim' => $borrowing->member->nis_nim,
                ],
                'books' => $borrowing->details->map(fn ($d) => [
                    'id' => $d->id,
                    'title' => $d->book->title,
                    'book_code' => $d->book->book_code,
                    'author' => $d->book->author,
                    'cover' => $d->book->cover_url,
                    'status' => $d->status->value,
                    'returned_at' => $d->returned_at?->format('d M Y'),
                ]),
            ],
        ]);
    }

    /**
     * POST /api/borrowings/{id}/remind
     * Send reminder
     */
    public function remind(Borrowing $borrowing): JsonResponse
    {
        $sent = $this->borrowingService->sendReminder($borrowing);

        return response()->json([
            'success' => $sent,
            'message' => $sent ? 'Reminder berhasil dikirim.' : 'Gagal mengirim reminder.',
        ]);
    }

    private function getStatusLabel(Borrowing $borrowing): string
    {
        return match ($borrowing->status->value) {
            'pending' => 'Menunggu Verifikasi',
            'active' => $borrowing->isOverdue() ? 'Terlambat' : 'Aktif',
            'returned' => 'Dikembalikan',
            'late' => 'Dikembalikan (Terlambat)',
            default => ucfirst($borrowing->status->value),
        };
    }

    /**
     * POST /api/borrowings/{id}/approve
     * Approve a pending borrowing (admin action)
     */
    public function approve(Borrowing $borrowing): JsonResponse
    {
        try {
            $borrowing = $this->borrowingService->approve($borrowing, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil disetujui.',
                'data' => [
                    'id' => $borrowing->id,
                    'transaction_code' => $borrowing->transaction_code,
                    'status' => $borrowing->status->value,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/borrowings/{id}/reject
     * Reject a pending borrowing (admin action)
     */
    public function reject(Borrowing $borrowing): JsonResponse
    {
        try {
            $this->borrowingService->reject($borrowing);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman ditolak dan dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
