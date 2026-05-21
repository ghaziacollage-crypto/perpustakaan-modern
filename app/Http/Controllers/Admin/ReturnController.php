<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingDetailStatus;
use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Borrowings\StoreReturnRequest;
use App\Models\Borrowing;
use App\Services\BorrowingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(): View
    {
        $status = request('status');

        $query = Borrowing::with(['member', 'details.book']);

        if ($status === 'returned') {
            $query->where('status', BorrowingStatus::Returned);
        } elseif ($status === 'late') {
            $query->where('status', BorrowingStatus::Late)
                ->whereHas('details', fn ($q) => $q->where('status', BorrowingDetailStatus::Borrowed->value));
        } elseif ($status === 'active') {
            $query->where('status', BorrowingStatus::Active)
                ->whereHas('details', fn ($q) => $q->where('status', BorrowingDetailStatus::Borrowed->value));
        } else {
            $query->whereHas('details', fn ($q) => $q->where('status', BorrowingDetailStatus::Borrowed->value));
        }

        $borrowings = $query->latest()->paginate(10);

        return view('admin.returns.index', compact('borrowings', 'status'));
    }

    /**
     * GET /admin/returns/scan
     * Scan QR return code page
     */
    public function scanReturn(): View
    {
        return view('admin.returns.scan');
    }

    public function store(StoreReturnRequest $request, Borrowing $borrowing, BorrowingService $service): RedirectResponse
    {
        if (!$borrowing->details()->where('status', BorrowingDetailStatus::Borrowed->value)->exists()) {
            return redirect()->route('admin.returns.index')->with('error', 'Semua buku sudah dikembalikan.');
        }

        // Refresh $borrowing after service call so we have the UPDATED object
        $borrowing = $service->returnBorrowing($borrowing, $request->validated());

        $remaining = $borrowing->details()->where('status', BorrowingDetailStatus::Borrowed->value)->count();

        if ($remaining > 0) {
            $message = 'Pengembalian berhasil dicatat. Masih ada ' . $remaining . ' buku yang belum dikembalikan.';
        } elseif ($borrowing->status === BorrowingStatus::Late) {
            $message = 'Semua buku berhasil dikembalikan. ⚠️ TERLAMBAT!';
        } else {
            $message = 'Semua buku berhasil dikembalikan.';
        }

        return redirect()->route('admin.returns.index')->with('success', $message);
    }
}