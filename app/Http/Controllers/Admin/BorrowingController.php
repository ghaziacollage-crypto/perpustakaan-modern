<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Borrowings\StoreBorrowingRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BorrowingController extends Controller
{
    public function __construct(
        private readonly BorrowingService $borrowingService,
        private readonly ReceiptService $receiptService,
    ) {}

    public function index(Request $request): View
    {
        $statusParam = $request->query('status');
        $searchMember = $request->query('member');

        $query = Borrowing::with(['member', 'details.book']);

        if ($statusParam && BorrowingStatus::tryFrom($statusParam)) {
            $query->where('status', $statusParam);
        }

        if ($searchMember) {
            $query->whereHas('member', fn ($q) => $q
                ->where('name', 'like', "%{$searchMember}%")
                ->orWhere('member_code', 'like', "%{$searchMember}%"));
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        // Count per status for filter badges
        $totalAll = Borrowing::count();
        $countPending = Borrowing::where('status', BorrowingStatus::Pending)->count();
        $countActive = Borrowing::where('status', BorrowingStatus::Active)->count();
        $countLate = Borrowing::where('status', BorrowingStatus::Late)->count();
        $countReturned = Borrowing::where('status', BorrowingStatus::Returned)->count();

        $members = Member::orderBy('name')->get();
        $books = Book::orderBy('title')->get();

        return view('admin.borrowings.index', compact(
            'borrowings', 'members', 'books',
            'statusParam', 'totalAll', 'countPending', 'countActive', 'countLate', 'countReturned', 'searchMember'
        ));
    }

    /**
     * Halaman create borrowing — step-by-step wizard
     */
    public function create(): View
    {
        $members = Member::orderBy('name')->get();
        $loanDuration = $this->borrowingService->getLoanDuration();
        $defaultDueDate = $this->borrowingService->getDefaultDueDate()->toDateString();

        return view('admin.borrowings.create', compact(
            'members', 'loanDuration', 'defaultDueDate'
        ));
    }

    public function store(StoreBorrowingRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $member = Member::findOrFail($request->integer('member_id'));
            $dueDate = $request->has('due_date')
                ? Carbon::parse($request->input('due_date'))
                : null;

            $borrowings = $this->borrowingService->createBorrowings(
                $member,
                $request->input('book_ids', []),
                $dueDate,
                $request->input('notes')
            );
            $borrowing = $borrowings->first();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peminjaman berhasil disimpan.',
                    'data' => [
                        'borrowing_id' => $borrowing->id,
                        'transaction_code' => $borrowing->transaction_code,
                        'total_borrowings' => $borrowings->count(),
                        'borrowings' => $borrowings->map(fn (Borrowing $item) => [
                            'id' => $item->id,
                            'transaction_code' => $item->transaction_code,
                        ])->values(),
                        'receipt_url' => route('admin.borrowings.receipt', $borrowing),
                    ],
                ], 201);
            }

            return redirect()
                ->route('admin.borrowings.receipt', $borrowing)
                ->with('success', 'Peminjaman berhasil disimpan. Silakan cetak struk.');

        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function remind(Borrowing $borrowing): RedirectResponse
    {
        if (! in_array($borrowing->status, [BorrowingStatus::Active, BorrowingStatus::Late], true)) {
            return redirect()->route('admin.borrowings.index')->with('error', 'Transaksi sudah selesai.');
        }

        $sent = $this->borrowingService->sendReminder($borrowing);

        return redirect()->route('admin.borrowings.index')
            ->with($sent ? 'success' : 'error', $sent ? 'Reminder WhatsApp terkirim.' : 'Gagal mengirim reminder.');
    }

    /**
     * Show receipt page after successful borrowing
     */
    public function approve(Borrowing $borrowing): RedirectResponse
    {
        try {
            $this->borrowingService->approve($borrowing, auth()->id());

            return redirect()
                ->route('admin.borrowings.index')
                ->with('success', 'Peminjaman berhasil disetujui. Notifikasi WhatsApp telah dikirim.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.borrowings.index')
                ->with('error', 'Gagal menyetujui peminjaman: ' . $e->getMessage());
        }
    }

    public function reject(Borrowing $borrowing): RedirectResponse
    {
        try {
            $this->borrowingService->reject($borrowing);

            return redirect()
                ->route('admin.borrowings.index')
                ->with('success', 'Peminjaman berhasil ditolak dan dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.borrowings.index')
                ->with('error', 'Gagal menolak peminjaman: ' . $e->getMessage());
        }
    }

    public function receipt(Borrowing $borrowing): View
    {
        $borrowing->load(['member', 'details.book']);

        if (request()->wantsJson()) {
            $data = $this->receiptService->generateReceiptData($borrowing);

            return response()->json(['success' => true, 'data' => $data]);
        }

        return view('admin.borrowings.receipt-page', compact('borrowing'));
    }

    /**
     * Download receipt as PDF
     */
    public function receiptPdf(Borrowing $borrowing): \Illuminate\Http\Response
    {
        return $this->receiptService->downloadPdf($borrowing);
    }
}
