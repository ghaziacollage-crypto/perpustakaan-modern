<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BorrowingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BorrowingLookupController extends Controller
{
    public function __construct(
        private readonly BorrowingService $borrowingService,
    ) {}

    /**
     * GET /admin/members/lookup?code={code}
     * Lookup member by QR code — returns JSON for AJAX
     */
    public function lookupMember(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json([
                'success' => false,
                'error' => 'Kode member diperlukan.',
            ], 400);
        }

        $result = $this->borrowingService->validateMemberByCode($code);

        if (! $result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * GET /admin/books/lookup?code={code}
     * Lookup book by QR code — returns JSON for AJAX
     */
    public function lookupBook(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json([
                'success' => false,
                'error' => 'Kode buku diperlukan.',
            ], 400);
        }

        $result = $this->borrowingService->validateBookByCode($code);

        if (! $result['success']) {
            return response()->json($result, $result['error'] === 'Buku tidak ditemukan.' ? 404 : 409);
        }

        return response()->json($result);
    }

    /**
     * GET /admin/settings/borrowing
     * Get borrowing settings
     */
    public function settings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'loan_duration_days' => $this->borrowingService->getLoanDuration(),
                'default_due_date' => $this->borrowingService->getDefaultDueDate()->toDateString(),
                'max_borrowings_per_member' => \App\Models\Member::MAX_BORROWINGS,
            ],
        ]);
    }

    /**
     * GET /admin/borrowings/lookup-by-code?code={transactionCode}
     * Lookup borrowing by transaction code for return scan — returns JSON
     */
    public function lookupByTransactionCode(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json([
                'success' => false,
                'error' => 'Kode transaksi diperlukan.',
            ], 400);
        }

        $result = $this->borrowingService->findByTransactionCode($code);

        if (! $result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }
}
