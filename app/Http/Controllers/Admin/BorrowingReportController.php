<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingDetailStatus;
use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Services\ReportPdfService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BorrowingReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->string('period')->toString() ?: 'custom';
        $startDate = $request->input('start_date') ? (string) $request->input('start_date') : now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ? (string) $request->input('end_date') : now()->toDateString();
        $type = $request->string('type')->toString() ?: 'both';

        // Borrowings
        $borrowingQuery = Borrowing::with(['member', 'details.book'])
            ->orderByDesc('loan_date');

        if ($type !== 'return') {
            $borrowingQuery = $this->applyDateFilter($borrowingQuery, $period, $startDate, $endDate, 'loan_date');
        }
        if ($type === 'return') {
            $borrowingQuery->whereNotNull('return_date');
        }
        if ($class = $request->string('class_filter')->toString()) {
            $borrowingQuery->whereHas('member', fn ($q) => $q->where('class', $class));
        }
        if ($search = $request->string('search')->toString()) {
            $borrowingQuery->whereHas('member', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $borrowings = $borrowingQuery->paginate(30)->withQueryString();

        // Returns
        $returnQuery = Borrowing::with(['member', 'details.book'])
            ->whereNotNull('return_date')
            ->orderByDesc('return_date');

        if ($type !== 'borrowing') {
            $returnQuery = $this->applyDateFilter($returnQuery, $period, $startDate, $endDate, 'return_date');
        }
        if ($type === 'borrowing') {
            $returnQuery->whereNull('return_date');
        }
        if ($class = $request->string('class_filter')->toString()) {
            $returnQuery->whereHas('member', fn ($q) => $q->where('class', $class));
        }

        $returns = $returnQuery->paginate(30)->withQueryString();

        $data = compact('borrowings', 'returns', 'period', 'startDate', 'endDate', 'type');
        $data['report_title'] = 'Laporan Peminjaman & Pengembalian';

        return view('admin.reports.borrowings.index', $data);
    }

    public function exportPdf(Request $request, ReportPdfService $pdfService): \Illuminate\Http\Response
    {
        $period = $request->string('period')->toString() ?: 'custom';
        $startDate = $request->input('start_date') ? (string) $request->input('start_date') : now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ? (string) $request->input('end_date') : now()->toDateString();
        $type = $request->string('type')->toString() ?: 'both';

        $borrowingQuery = Borrowing::with(['member', 'details.book'])
            ->orderByDesc('loan_date');

        if ($type !== 'return') {
            $borrowingQuery = $this->applyDateFilter($borrowingQuery, $period, $startDate, $endDate, 'loan_date');
        }
        if ($type === 'return') {
            $borrowingQuery->whereNotNull('return_date');
        }
        if ($class = $request->string('class_filter')->toString()) {
            $borrowingQuery->whereHas('member', fn ($q) => $q->where('class', $class));
        }

        $borrowings = $borrowingQuery->get();

        $returnQuery = Borrowing::with(['member', 'details.book'])
            ->whereNotNull('return_date')
            ->orderByDesc('return_date');

        if ($type !== 'borrowing') {
            $returnQuery = $this->applyDateFilter($returnQuery, $period, $startDate, $endDate, 'return_date');
        }
        if ($type === 'borrowing') {
            $returnQuery->whereNull('return_date');
        }
        if ($class = $request->string('class_filter')->toString()) {
            $returnQuery->whereHas('member', fn ($q) => $q->where('class', $class));
        }

        $returns = $returnQuery->get();

        $subtitle = $this->formatDateRange($period, $startDate, $endDate);

        return $pdfService->generatePdf('admin.reports.borrowings.pdf', [
            'borrowings' => $borrowings,
            'returns' => $returns,
            'report_type' => $type,
            'report_title' => 'Laporan Peminjaman & Pengembalian',
            'report_subtitle' => $subtitle,
        ], 'Laporan-Peminjaman.pdf');
    }

    public function pdf(Request $request, ReportPdfService $pdfService): \Illuminate\Http\Response
    {
        return $this->exportPdf($request, $pdfService);
    }

    private function applyDateFilter($query, string $period, string $startDate, string $endDate, string $dateColumn)
    {
        return match ($period) {
            'day' => $query->whereDate($dateColumn, $startDate),
            'week' => $query->whereBetween($dateColumn, [
                \Carbon\Carbon::parse($startDate)->startOfWeek()->toDateString(),
                \Carbon\Carbon::parse($startDate)->endOfWeek()->toDateString(),
            ]),
            'month' => $query->whereYear($dateColumn, \Carbon\Carbon::parse($startDate)->year)
                ->whereMonth($dateColumn, \Carbon\Carbon::parse($startDate)->month),
            'year' => $query->whereYear($dateColumn, (int) $startDate),
            default => $query->whereBetween($dateColumn, [$startDate, $endDate . ' 23:59:59']),
        };
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
