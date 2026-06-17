<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Borrowing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptService
{
    /**
     * Generate receipt data array (for API/JS use)
     */
    public function generateReceiptData(Borrowing $borrowing): array
    {
        $borrowing->load(['member', 'details.book']);

        $appName = app_setting('app_name', 'Perpustakaan Modern');
        $appAddress = app_setting('library_address', 'Jl. Perpustakaan No. 1');
        $appPhone = app_setting('library_phone', '-');

        $books = $borrowing->details->map(fn ($detail) => [
            'num' => $detail->loop ?? 0,
            'title' => $detail->book->title,
            'book_code' => $detail->book->book_code,
            'author' => $detail->book->author,
        ])->toArray();

        $totalBooks = count($books);
        $dueDate = Carbon::parse($borrowing->due_date);
        $isOverdue = $borrowing->isOverdue();
        $daysLeft = $borrowing->daysUntilDue();
        $statusLabel = $borrowing->status->value === 'returned'
            ? 'Dikembalikan'
            : ($isOverdue ? 'Terlambat '.$borrowing->daysOverdue().' hari' : $borrowing->dueCountdownLabel());

        return [
            'transaction_code' => $borrowing->transaction_code,
            'app_name' => $appName,
            'app_address' => $appAddress,
            'app_phone' => $appPhone,
            'logo_url' => app_setting('logo') ? asset('storage/'.app_setting('logo')) : null,
            'member' => [
                'name' => $borrowing->member->name,
                'code' => $borrowing->member->member_code,
                'nis_nim' => $borrowing->member->nis_nim,
                'class' => $borrowing->member->class,
                'photo' => $borrowing->member->photo ? asset('storage/'.$borrowing->member->photo) : null,
            ],
            'loan_date' => Carbon::parse($borrowing->loan_date)->format('d M Y'),
            'due_date' => $dueDate->format('d M Y'),
            'return_date' => $borrowing->return_date ? Carbon::parse($borrowing->return_date)->format('d M Y') : null,
            'status' => $borrowing->status->value,
            'status_label' => $statusLabel,
            'is_overdue' => $isOverdue,
            'total_books' => $totalBooks,
            'books' => $books,
            'notes' => $borrowing->notes,
            'borrowing' => $borrowing, // ⬅️ lempar object ke view untuk daysOverdue()
            'printed_at' => now()->format('d M Y, H:i').' WIB',
            'printed_by' => auth()->user()?->name ?? 'Admin',
        ];
    }

    /**
     * Generate PDF receipt
     */
    public function generatePdf(Borrowing $borrowing): \Barryvdh\DomPDF\PDF
    {
        $data = $this->generateReceiptData($borrowing);

        $pdf = Pdf::loadView('admin.borrowings.receipt-pdf', $data);
        $pdf->setPaper([0, 0, 283.46, 600], 'portrait'); // ~80mm thermal paper

        return $pdf;
    }

    /**
     * Download PDF receipt
     */
    public function downloadPdf(Borrowing $borrowing): \Illuminate\Http\Response
    {
        $pdf = $this->generatePdf($borrowing);
        $filename = 'STRUK-'.$borrowing->transaction_code.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get HTML receipt for printing (browser print)
     */
    public function getPrintHtml(Borrowing $borrowing): string
    {
        $data = $this->generateReceiptData($borrowing);

        return View::make('admin.borrowings.receipt-pdf', $data)->render();
    }
}
