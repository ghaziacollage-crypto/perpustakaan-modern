<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BookCondition;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Services\ReportPdfService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->string('period')->toString() ?: 'custom';
        $startDate = $request->input('start_date') ? (string) $request->input('start_date') : now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ? (string) $request->input('end_date') : now()->toDateString();

        // Condition stats
        $totalBooks = Book::count();
        $totalNormal = Book::where('kondisi', BookCondition::Normal->value)->count();
        $totalRusak = Book::where('kondisi', BookCondition::Rusak->value)->count();
        $totalHilang = Book::where('kondisi', BookCondition::Hilang->value)->count();

        // Borrow counts for period
        $borrowCounts = $this->getBorrowCounts($period, $startDate, $endDate);

        // Books with borrow counts, filtered by category
        $categoryId = $request->integer('category');
        $query = Book::with('category')
            ->leftJoinSub($borrowCounts, 'bc', function ($join) {
                $join->on('books.id', '=', 'bc.book_id');
            })
            ->select('books.*', \DB::raw('COALESCE(bc.total, 0) as borrow_count'));

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Class filter: books borrowed by members of a specific class
        if ($class = $request->string('class_filter')->toString()) {
            $query->whereHas('borrowingDetails.borrowing.member', fn ($q) => $q->where('class', $class));
        }

        $books = $query->orderByDesc('borrow_count')->orderBy('books.title')->paginate(20);

        // Top 10 most borrowed
        $topBooks = Book::with('category')
            ->leftJoinSub($borrowCounts, 'bc_top', function ($join) {
                $join->on('books.id', '=', 'bc_top.book_id');
            })
            ->select('books.*', \DB::raw('COALESCE(bc_top.total, 0) as borrow_count'))
            ->orderByDesc('borrow_count')
            ->take(10)
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('admin.reports.books.index', compact(
            'books',
            'topBooks',
            'totalBooks',
            'totalNormal',
            'totalRusak',
            'totalHilang',
            'categories',
            'period',
            'startDate',
            'endDate',
        ));
    }

    public function exportPdf(Request $request, ReportPdfService $pdfService)
    {
        $period = $request->string('period')->toString() ?: 'custom';
        $startDate = $request->input('start_date') ? (string) $request->input('start_date') : now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ? (string) $request->input('end_date') : now()->toDateString();

        $borrowCounts = $this->getBorrowCounts($period, $startDate, $endDate);

        $categoryId = $request->integer('category');
        $query = Book::with('category')
            ->leftJoinSub($borrowCounts, 'bc', function ($join) {
                $join->on('books.id', '=', 'bc.book_id');
            })
            ->select('books.*', \DB::raw('COALESCE(bc.total, 0) as borrow_count'));

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($class = $request->string('class_filter')->toString()) {
            $query->whereHas('borrowingDetails.borrowing.member', fn ($q) => $q->where('class', $class));
        }

        $books = $query->orderByDesc('borrow_count')->orderBy('books.title')->get()->values();

        $subtitle = $this->formatDateRange($period, $startDate, $endDate);

        return $pdfService->generatePdf('admin.reports.books.pdf', [
            'books' => $books,
            'report_title' => 'Laporan Kriteria Buku',
            'report_subtitle' => $subtitle,
        ], 'Laporan-Kriteria-Buku.pdf');
    }

    public function pdf(Request $request, ReportPdfService $pdfService)
    {
        return $this->exportPdf($request, $pdfService);
    }

    private function getBorrowCounts(string $period, string $startDate, string $endDate)
    {
        $baseQuery = BorrowingDetail::select('book_id');

        switch ($period) {
            case 'day':
                $baseQuery->whereHas('borrowing', fn ($q) => $q->whereDate('loan_date', $startDate));
                break;
            case 'week':
                $baseQuery->whereHas('borrowing', fn ($q) => $q->whereBetween('loan_date', [
                    \Carbon\Carbon::parse($startDate)->startOfWeek()->toDateString(),
                    \Carbon\Carbon::parse($startDate)->endOfWeek()->toDateString(),
                ]));
                break;
            case 'month':
                $baseQuery->whereHas('borrowing', fn ($q) => $q->whereYear('loan_date', \Carbon\Carbon::parse($startDate)->year)
                    ->whereMonth('loan_date', \Carbon\Carbon::parse($startDate)->month));
                break;
            case 'year':
                $baseQuery->whereHas('borrowing', fn ($q) => $q->whereYear('loan_date', (int) $startDate));
                break;
            default:
                $baseQuery->whereHas('borrowing', fn ($q) => $q->whereBetween('loan_date', [$startDate, $endDate . ' 23:59:59']));
                break;
        }

        return $baseQuery->groupBy('book_id')
            ->selectRaw('book_id, COUNT(*) as total')
            ->toBase();
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
