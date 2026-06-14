<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Services\ReportPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FineReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Fine::with(['member', 'borrowing.details.book']);

        // Period filter
        $period = $request->string('period')->toString() ?: 'custom';
        $startDate = $request->input('start_date') ? (string) $request->input('start_date') : now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ? (string) $request->input('end_date') : now()->toDateString();

        $query = $this->applyDateFilter($query, $period, $startDate, $endDate);

        // Status filter
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        // Class filter
        if ($class = $request->string('class_filter')->toString()) {
            $query->whereHas('member', fn ($q) => $q->where('class', $class));
        }

        // Search
        if ($search = $request->string('search')->toString()) {
            $query->whereHas('member', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $fines = $this->orderByBorrowingDueDate($query)->paginate(30)->withQueryString();

        // All distinct classes for filter dropdown
        $classes = Fine::join('members', 'fines.member_id', '=', 'members.id')
            ->distinct()
            ->pluck('members.class')
            ->filter()
            ->sort()
            ->values();

        // Total count
        $totalFines = Fine::count();

        $data = compact('fines', 'totalFines', 'classes', 'period', 'startDate', 'endDate');
        $data['report_title'] = 'Laporan Keterlambatan';

        return view('admin.reports.fines.index', $data);
    }

    public function exportPdf(Request $request, ReportPdfService $pdfService): \Illuminate\Http\Response
    {
        $query = Fine::with(['member', 'borrowing.details.book']);

        $period = $request->string('period')->toString() ?: 'custom';
        $startDate = $request->input('start_date') ? (string) $request->input('start_date') : now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ? (string) $request->input('end_date') : now()->toDateString();

        $query = $this->applyDateFilter($query, $period, $startDate, $endDate);

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($class = $request->string('class_filter')->toString()) {
            $query->whereHas('member', fn ($q) => $q->where('class', $class));
        }

        if ($search = $request->string('search')->toString()) {
            $query->whereHas('member', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $fines = $this->orderByBorrowingDueDate($query)->get();

        $subtitle = $this->formatDateRange($period, $startDate, $endDate);

        return $pdfService->generatePdf('admin.reports.fines.pdf', [
            'fines' => $fines,
            'report_title' => 'Laporan Keterlambatan',
            'report_subtitle' => $subtitle,
        ], 'Laporan-Keterlambatan.pdf');
    }

    public function pdf(Request $request, ReportPdfService $pdfService): \Illuminate\Http\Response
    {
        return $this->exportPdf($request, $pdfService);
    }

    public function byMember(Request $request): View
    {
        $memberId = $request->integer('member_id');
        $members = Member::orderBy('name')->get();
        $selectedMember = null;
        $fines = collect();

        if ($memberId) {
            $selectedMember = Member::withCount(['borrowings as total_borrowings' => fn ($q) => $q->whereNotNull('loan_date')])->find($memberId);
            $fines = Fine::with(['member', 'borrowing.details.book'])
                ->where('member_id', $memberId)
                ->orderByDesc(Borrowing::select('due_date')
                    ->whereColumn('borrowings.id', 'fines.borrowing_id')
                    ->limit(1))
                ->get();
        }

        return view('admin.reports.fines.by-member', compact(
            'members', 'selectedMember', 'fines'
        ));
    }

    public function pdfByMember(Member $member, ReportPdfService $pdfService): \Illuminate\Http\Response
    {
        $fines = Fine::with(['member', 'borrowing.details.book'])
            ->where('member_id', $member->id)
            ->orderByDesc(Borrowing::select('due_date')
                ->whereColumn('borrowings.id', 'fines.borrowing_id')
                ->limit(1))
            ->get();

        return $pdfService->generatePdf('admin.reports.fines.pdf', [
            'fines' => $fines,
            'report_title' => "Laporan Keterlambatan - {$member->name}",
            'report_subtitle' => '',
        ], "Keterlambatan-{$member->member_code}.pdf");
    }

    private function applyDateFilter($query, string $period, string $startDate, string $endDate)
    {
        return match ($period) {
            'day' => $query->whereDate('created_at', $startDate),
            'week' => $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($startDate)->startOfWeek()->toDateString(),
                \Carbon\Carbon::parse($startDate)->endOfWeek()->toDateString(),
            ]),
            'month' => $query->whereYear('created_at', \Carbon\Carbon::parse($startDate)->year)
                ->whereMonth('created_at', \Carbon\Carbon::parse($startDate)->month),
            'year' => $query->whereYear('created_at', (int) $startDate),
            default => $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']),
        };
    }

    private function orderByBorrowingDueDate($query)
    {
        return $query->orderByDesc(Borrowing::select('due_date')
            ->whereColumn('borrowings.id', 'fines.borrowing_id')
            ->limit(1));
    }

    private function formatDateRange(string $period, string $start, string $end): string
    {
        return match ($period) {
            'day' => 'Tanggal ' . \Carbon\Carbon::parse($start)->isoFormat('D MMMM Y'),
            'week' => 'Minggu ' . \Carbon\Carbon::parse($start)->isoFormat('D MMMM Y'),
            'month' => \Carbon\Carbon::parse($start)->isoFormat('MMMM Y'),
            'year' => \Carbon\Carbon::parse($start)->isoFormat('Y'),
            default => \Carbon\Carbon::parse($start)->isoFormat('D MMMM Y') . ' s/d ' . \Carbon\Carbon::parse($end)->isoFormat('D MMMM Y'),
        };
    }
}
