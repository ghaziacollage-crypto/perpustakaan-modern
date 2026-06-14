<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        // Borrowing stats by month
        $monthlyStats = $this->getMonthlyStats($year);

        // Most borrowed books
        $popularBooks = BorrowingDetail::selectRaw('book_id, COUNT(*) as total')
            ->whereHas('borrowing', fn ($q) => $q->whereYear('loan_date', $year))
            ->with('book:id,title,book_code')
            ->groupBy('book_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Most active members
        $activeMembers = Borrowing::selectRaw('member_id, COUNT(*) as total')
            ->whereYear('loan_date', $year)
            ->with('member:id,name,member_code')
            ->groupBy('member_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Overdue list
        $overdueList = Borrowing::with(['member:id,name,member_code', 'details.book:id,title'])
            ->where('status', BorrowingStatus::Active->value)
            ->whereDate('due_date', '<', now()->toDateString())
            ->latest('due_date')
            ->limit(20)
            ->get();

        // Summary cards
        $summary = [
            'total_borrowings' => Borrowing::whereYear('loan_date', $year)->count(),
            'active_borrowings' => Borrowing::where('status', BorrowingStatus::Active->value)->count(),
            'overdue_count' => Borrowing::where('status', BorrowingStatus::Active->value)
                ->whereDate('due_date', '<', now()->toDateString())->count(),
            'late_returns' => Borrowing::where('status', BorrowingStatus::Late->value)
                ->whereYear('loan_date', $year)->count(),
        ];

        // Monthly chart data
        $chartData = $this->getMonthlyChartData($year);

        return view('admin.reports.index', compact(
            'summary', 'monthlyStats', 'popularBooks', 'activeMembers',
            'overdueList', 'chartData', 'year'
        ));
    }

    private function getMonthlyStats(int $year): array
    {
        $stats = [];

        for ($m = 1; $m <= 12; $m++) {
            $stats[$m] = Borrowing::whereYear('loan_date', $year)
                ->whereMonth('loan_date', $m)
                ->count();
        }

        return $stats;
    }

    private function getMonthlyChartData(int $year): array
    {
        $labels = [];
        $data = [];

        $indonesianMonths = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = $indonesianMonths[$m];
            $data[] = Borrowing::whereYear('loan_date', $year)
                ->whereMonth('loan_date', $m)
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }
}