<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\BorrowingDetailStatus;
use App\Enums\BorrowingStatus;
use App\Enums\MemberStatus;
use App\Http\Requests\Members\RegisterMemberRequest;
use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Services\BorrowingService;
use App\Services\MemberAttendanceService;
use App\Services\MemberPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function __construct(
        private readonly MemberAttendanceService $attendanceService,
        private readonly BorrowingService $borrowingService,
        private readonly MemberPhotoService $memberPhoto,
    ) {}

    /**
     * GET /member/register
     * Show registration form for new members
     */
    public function showRegisterForm(): View
    {
        return view('landing.member.register');
    }

    /**
     * POST /member/register
     * Process member registration (status: Pending, needs admin approval)
     */
    public function register(RegisterMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auto-generate unique member code
        $data['member_code'] = $this->generateMemberCode();
        $data['status'] = MemberStatus::Pending;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->memberPhoto->upload($request->file('photo'));
        }

        Member::create($data);

        return redirect()->route('member.register')
            ->with('success', 'Pendaftaran berhasil! Silakan tunggu persetujuan admin sebelum bisa mengakses.');
    }

    private function generateMemberCode(): string
    {
        do {
            $code = 'MBR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Member::where('member_code', $code)->exists());

        return $code;
    }

    /**
     * GET /member
     * Landing page for members — shows scan QR form
     */
    public function index(): View
    {
        return view('landing.member.index');
    }

    /**
     * GET /member/lookup?code={member_code}
     * Look up member info by QR code (public — no auth)
     */
    public function lookup(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json(['success' => false, 'error' => 'Kode member diperlukan.'], 400);
        }

        $member = Member::where('member_code', $code)->first();

        if (! $member) {
            return response()->json(['success' => false, 'error' => 'Member tidak ditemukan.'], 404);
        }

        $borrowings = Borrowing::where('member_id', $member->id)
            ->whereIn('status', [BorrowingStatus::Pending, BorrowingStatus::Active, BorrowingStatus::Late])
            ->with(['details.book.category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'member_code' => $member->member_code,
                'nis_nim' => $member->nis_nim,
                'class' => $member->class,
                'major' => $member->major,
                'photo' => $member->qr_code_url,
                'is_active' => $member->isActive(),
                'remaining_slots' => $member->remaining_slots,
                'active_borrowings_count' => $member->active_borrowings_count,
            ],
            'borrowings' => $borrowings->map(fn ($b) => [
                'id' => $b->id,
                'transaction_code' => $b->transaction_code,
                'status' => $b->status->value,
                'status_label' => $this->getStatusLabel($b),
                'loan_date' => $b->loan_date->format('d M Y'),
                'due_date' => $b->due_date->format('d M Y'),
                'is_overdue' => $b->isOverdue(),
                'books' => $b->details->map(fn ($d) => [
                    'id' => $d->id,
                    'title' => $d->book->title,
                    'author' => $d->book->author,
                    'cover' => $d->book->cover_url,
                    'category' => $d->book->category?->name,
                    'is_returned' => $d->status === BorrowingDetailStatus::Returned,
                ]),
            ]),
        ]);
    }

    /**
     * GET /member/dashboard?code={member_code}
     * Member dashboard after scan
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return redirect()->route('member.index');
        }

        $member = Member::where('member_code', $code)->first();

        if (! $member) {
            return redirect()->route('member.index')->with('error', 'Member tidak ditemukan.');
        }

        // Stats
        $pendingCount = Borrowing::where('member_id', $member->id)
            ->where('status', BorrowingStatus::Pending)->count();

        $activeBorrowings = Borrowing::where('member_id', $member->id)
            ->whereIn('status', [BorrowingStatus::Active, BorrowingStatus::Late])
            ->with(['details.book'])
            ->latest()
            ->limit(5)
            ->get();

        $lateCount = Borrowing::where('member_id', $member->id)
            ->where('status', BorrowingStatus::Late)->count();

        return view('landing.member.dashboard', [
            'member' => $member,
            'pendingCount' => $pendingCount,
            'activeBorrowings' => $activeBorrowings,
            'lateCount' => $lateCount,
        ]);
    }

    /**
     * GET /member/books?code={member_code}
     * Select books page for member
     */
    public function selectBook(Request $request): View|RedirectResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return redirect()->route('member.index');
        }

        $member = Member::where('member_code', $code)->first();

        if (! $member) {
            return redirect()->route('member.index');
        }

        $search = $request->get('search', '');
        $categoryId = $request->get('category');

        $query = Book::with('category')
            ->where('stock', '>', 0)
            ->where('status', 'available');

        if ($search) {
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%"));
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->latest()->paginate(20)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('landing.member.borrow', [
            'member' => $member,
            'books' => $books,
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    /**
     * GET /member/borrowings?code={member_code}
     * My borrowings history
     */
    public function myBorrowings(Request $request): View|RedirectResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return redirect()->route('member.index');
        }

        $member = Member::where('member_code', $code)->first();

        if (! $member) {
            return redirect()->route('member.index');
        }

        $status = $request->get('status');

        $query = Borrowing::where('member_id', $member->id)
            ->with(['details.book']);

        if ($status && in_array($status, ['pending', 'active', 'returned', 'late'])) {
            $query->where('status', BorrowingStatus::from($status));
        }

        $borrowings = $query->latest()->paginate(15)->withQueryString();

        return view('landing.member.history', [
            'member' => $member,
            'borrowings' => $borrowings,
            'statusFilter' => $status,
        ]);
    }

    /**
     * POST /member/borrow
     * Request a new borrowing (member selects books)
     */
    public function requestBorrow(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string'],
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['integer'],
        ]);

        $member = Member::where('member_code', $validated['member_code'])->first();

        if (! $member) {
            return response()->json(['success' => false, 'error' => 'Member tidak ditemukan.'], 404);
        }

        if (! $member->isActive()) {
            return response()->json(['success' => false, 'error' => 'Status member tidak aktif.'], 403);
        }

        if (! $member->canBorrow()) {
            return response()->json([
                'success' => false,
                'error' => 'Slot peminjaman habis. Sisa: ' . $member->remaining_slots,
            ], 422);
        }

        // Check remaining slots
        if (count($validated['book_ids']) > $member->remaining_slots) {
            return response()->json([
                'success' => false,
                'error' => 'Melebihi slot. Sisa: ' . $member->remaining_slots . ' buku',
            ], 422);
        }

        try {
            $borrowing = $this->borrowingService->createPending(
                $member,
                $validated['book_ids']
            );

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diajukan. Menunggu verifikasi admin.',
                'data' => [
                    'id' => $borrowing->id,
                    'transaction_code' => $borrowing->transaction_code,
                    'status' => $borrowing->status->value,
                    'total_books' => $borrowing->details->count(),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->errors()['book_ids'][0] ?? 'Gagal mengajukan peminjaman.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /member/borrow/{id}/cancel
     * Cancel a pending borrowing
     */
    public function cancelBorrow(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string'],
        ]);

        $member = Member::where('member_code', $validated['member_code'])->first();

        if (! $member) {
            return response()->json(['success' => false, 'error' => 'Member tidak ditemukan.'], 404);
        }

        $borrowing = Borrowing::where('id', $id)
            ->where('member_id', $member->id)
            ->where('status', BorrowingStatus::Pending)
            ->first();

        if (! $borrowing) {
            return response()->json([
                'success' => false,
                'error' => 'Peminjaman tidak ditemukan atau tidak bisa dibatalkan.',
            ], 404);
        }

        // Delete details first, then borrowing
        $borrowing->details()->delete();
        $borrowing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibatalkan.',
        ]);
    }

    /**
     * GET /member/borrowings/{id}/return-qr?code={member_code}
     * Generate return QR code for a borrowing
     */
    public function returnQr(Request $request, int $id): View|RedirectResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return redirect()->route('member.index');
        }

        $member = Member::where('member_code', $code)->first();

        if (! $member) {
            return redirect()->route('member.index');
        }

        $borrowing = Borrowing::where('id', $id)
            ->where('member_id', $member->id)
            ->whereIn('status', [BorrowingStatus::Active, BorrowingStatus::Late])
            ->with(['details.book'])
            ->first();

        if (! $borrowing) {
            return redirect()->route('member.borrowings', ['code' => $code])
                ->with('error', 'Peminjaman aktif tidak ditemukan.');
        }

        return view('landing.member.return-qr', [
            'member' => $member,
            'borrowing' => $borrowing,
        ]);
    }

    private function getStatusLabel($borrowing): string
    {
        return match ($borrowing->status->value) {
            'pending' => 'Menunggu Verifikasi',
            'active' => $borrowing->isOverdue() ? 'Terlambat' : 'Aktif',
            'returned' => 'Dikembalikan',
            'late' => 'Dikembalikan (Terlambat)',
            default => ucfirst($borrowing->status->value),
        };
    }
}