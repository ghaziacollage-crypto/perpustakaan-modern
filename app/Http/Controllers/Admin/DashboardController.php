<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\MemberAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalBooks = Book::count();
        $totalMembers = Member::count();
        $borrowedBooks = Borrowing::whereIn('status', [BorrowingStatus::Active, BorrowingStatus::Late])->count();
        $overdueBooks = Borrowing::where('status', BorrowingStatus::Late)->count();

        $recentBorrowings = Borrowing::with('member')
            ->latest()
            ->limit(5)
            ->get();

        // Pending borrowings (waiting for admin approval)
        $pendingBorrowings = Borrowing::with(['member', 'details.book'])
            ->where('status', BorrowingStatus::Pending)
            ->orderBy('created_at', 'asc')
            ->get();

        // Members currently at library (scanned in)
        $activeAttendances = MemberAttendance::active()
            ->with('member')
            ->orderBy('scanned_at', 'desc')
            ->get();

        // Chart 1: Peminjaman per Bulan (12 bulan terakhir)
        $monthlyBorrowings = Borrowing::select(
            DB::raw("DATE_FORMAT(loan_date, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total')
        )
            ->where('loan_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => $row->month,
                'label' => \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y'),
                'total' => (int) $row->total,
            ]);

        // Chart 2: Buku per Kategori (top 8)
        $categoryBooks = Category::withCount('books')
            ->orderByDesc('books_count')
            ->take(8)
            ->get()
            ->map(fn ($cat) => [
                'name' => $cat->name,
                'count' => (int) $cat->books_count,
            ]);

        // Chart 3: Status Peminjaman Saat Ini
        $borrowingStatusCounts = [
            'pending' => Borrowing::where('status', BorrowingStatus::Pending)->count(),
            'active' => Borrowing::where('status', BorrowingStatus::Active)->where('due_date', '>=', now()->toDateString())->count(),
            'late' => Borrowing::where('status', BorrowingStatus::Late)->count(),
            'returned' => Borrowing::where('status', BorrowingStatus::Returned)->whereDate('return_date', '>=', now()->subMonth()->toDateString())->count(),
        ];

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalMembers',
            'borrowedBooks',
            'overdueBooks',
            'recentBorrowings',
            'monthlyBorrowings',
            'categoryBooks',
            'borrowingStatusCounts',
            'pendingBorrowings',
            'activeAttendances',
        ));
    }
}