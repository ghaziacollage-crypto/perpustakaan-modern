<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingDetailStatus;
use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Services\WhatsAppService;
use App\Services\WhatsAppSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WhatsAppNotificationController extends Controller
{
    public function index(WhatsAppSettingsService $settings): View
    {
        return view('admin.whatsapp.index', [
            'connection' => $settings->getConnectionSummary(),
            'lastHealth' => session('wa_health'),
            'lastTest' => session('wa_test'),
        ]);
    }

    public function health(WhatsAppService $whatsApp): RedirectResponse
    {
        return redirect()
            ->route('admin.whatsapp.index')
            ->with('wa_health', $whatsApp->healthCheck());
    }

    public function sendTest(Request $request, WhatsAppService $whatsApp): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:1000'],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'message.required' => 'Isi pesan wajib diisi.',
        ]);

        $result = $whatsApp->sendMessageWithResult(null, $validated['phone'], $validated['message']);

        return redirect()
            ->route('admin.whatsapp.index')
            ->with('wa_test', $result)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function bulk(): View
    {
        $dueToday = $this->recipientQuery('due_today')->get();
        $overdue = $this->recipientQuery('overdue')->get();

        return view('admin.whatsapp.bulk', [
            'dueToday' => $dueToday,
            'overdue' => $overdue,
            'dueTodayTemplate' => $this->defaultTemplate('due_today'),
            'overdueTemplate' => $this->defaultTemplate('overdue'),
            'summary' => session('wa_bulk_summary'),
        ]);
    }

    public function sendBulk(Request $request, WhatsAppService $whatsApp): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:selected,due_today,overdue'],
            'selected' => ['nullable', 'array'],
            'selected.*' => ['integer'],
            'due_today_template' => ['required', 'string', 'max:2000'],
            'overdue_template' => ['required', 'string', 'max:2000'],
        ]);

        $borrowings = $this->resolveBulkRecipients($validated);
        $summary = [
            'total' => $borrowings->count(),
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
            'details' => [],
        ];

        foreach ($borrowings as $borrowing) {
            $member = $borrowing->member;
            $phone = (string) ($member?->whatsapp ?? '');

            if ($phone === '') {
                $summary['skipped']++;
                $summary['details'][] = [
                    'name' => $member?->name ?? '-',
                    'phone' => '-',
                    'status' => 'Dilewati',
                    'message' => 'Nomor WhatsApp kosong.',
                ];
                continue;
            }

            $type = $this->borrowingType($borrowing);
            $template = $type === 'overdue' ? $validated['overdue_template'] : $validated['due_today_template'];
            $message = $this->renderTemplate($template, $borrowing);
            $result = $whatsApp->sendMessageWithResult($member, $phone, $message);

            if ($result['success']) {
                $summary['sent']++;
            } else {
                $summary['failed']++;
            }

            $summary['details'][] = [
                'name' => $member?->name ?? '-',
                'phone' => $phone,
                'status' => $result['success'] ? 'Berhasil' : 'Gagal',
                'message' => $result['message'],
            ];
        }

        return redirect()
            ->route('admin.whatsapp.bulk')
            ->with('wa_bulk_summary', $summary)
            ->with('success', "Proses selesai. Berhasil: {$summary['sent']}, gagal: {$summary['failed']}, dilewati: {$summary['skipped']}.");
    }

    private function recipientQuery(string $type)
    {
        $query = Borrowing::with(['member', 'details.book'])
            ->whereIn('status', [BorrowingStatus::Active->value, BorrowingStatus::Late->value])
            ->whereNull('return_date')
            ->whereHas('details', fn ($q) => $q->where('status', BorrowingDetailStatus::Borrowed->value))
            ->orderBy('due_date')
            ->orderBy('loan_date');

        if ($type === 'due_today') {
            return $query->whereDate('due_date', now()->toDateString());
        }

        return $query->whereDate('due_date', '<', now()->toDateString());
    }

    private function resolveBulkRecipients(array $validated): Collection
    {
        if ($validated['mode'] === 'due_today') {
            return $this->recipientQuery('due_today')->get();
        }

        if ($validated['mode'] === 'overdue') {
            return $this->recipientQuery('overdue')->get();
        }

        $ids = collect($validated['selected'] ?? [])->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        return Borrowing::with(['member', 'details.book'])
            ->whereIn('id', $ids)
            ->whereNull('return_date')
            ->whereHas('details', fn ($q) => $q->where('status', BorrowingDetailStatus::Borrowed->value))
            ->orderBy('due_date')
            ->get();
    }

    private function defaultTemplate(string $type): string
    {
        if ($type === 'overdue') {
            return "Halo {nama},\n\nBuku berikut sudah terlambat dikembalikan:\n{judul_buku}\n\nTanggal pinjam: {tanggal_pinjam}\nJatuh tempo: {tanggal_jatuh_tempo}\nTerlambat: {jumlah_hari_terlambat} hari\n\nMohon segera dikembalikan ke perpustakaan. Terima kasih.";
        }

        return "Halo {nama},\n\nPengingat: buku berikut jatuh tempo hari ini:\n{judul_buku}\n\nTanggal pinjam: {tanggal_pinjam}\nJatuh tempo: {tanggal_jatuh_tempo}\n\nMohon dikembalikan tepat waktu. Terima kasih.";
    }

    private function renderTemplate(string $template, Borrowing $borrowing): string
    {
        $books = $this->bookTitles($borrowing);
        $daysLate = max(0, (int) $borrowing->due_date->diffInDays(now()));

        return strtr($template, [
            '{nama}' => (string) ($borrowing->member?->name ?? '-'),
            '{judul_buku}' => $books,
            '{tanggal_pinjam}' => $borrowing->loan_date->format('d M Y'),
            '{tanggal_jatuh_tempo}' => $borrowing->due_date->format('d M Y'),
            '{jumlah_hari_terlambat}' => (string) $daysLate,
        ]);
    }

    private function bookTitles(Borrowing $borrowing): string
    {
        return $borrowing->details
            ->where('status', BorrowingDetailStatus::Borrowed)
            ->map(fn ($detail): string => '- '.($detail->book?->title ?? '-'))
            ->implode("\n");
    }

    private function borrowingType(Borrowing $borrowing): string
    {
        return $borrowing->due_date->lt(now()->startOfDay()) ? 'overdue' : 'due_today';
    }
}
