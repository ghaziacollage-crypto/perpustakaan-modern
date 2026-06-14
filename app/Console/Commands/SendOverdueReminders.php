<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class SendOverdueReminders extends Command
{
    protected $signature = 'reminder:overdue';

    protected $description = 'Kirim reminder WhatsApp ke anggota yang terlambat mengembalikan buku';

    public function handle(WhatsAppService $whatsApp): int
    {
        $borrowings = Borrowing::with(['member', 'details.book'])
            ->where('status', BorrowingStatus::Active->value)
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereDoesntHave('bookReturn')
            ->get();

        if ($borrowings->isEmpty()) {
            $this->info('Tidak ada peminjaman yang terlambat.');

            return Command::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($borrowings as $borrowing) {
            $member = $borrowing->member;

            if (! $member?->whatsapp) {
                $this->warn("Anggota {$member?->name} tidak memiliki nomor WhatsApp.");
                $failed++;

                continue;
            }

            $bookTitles = $borrowing->details->pluck('book.title')->implode(', ');
            $daysLate = (int) max(0, $borrowing->due_date->diffInDays(now()));
            $dueDateFormatted = $borrowing->due_date->translatedFormat('d F Y');

            $message = "⚠️ PERHATIAN: Anda terlambat mengembalikan buku!\n\n";
            $message .= "Halo {$member->name},\n\n";
            $message .= "📚 Buku: {$bookTitles}\n";
            $message .= "📅 Jatuh Tempo: {$dueDateFormatted}\n";
            $message .= "⏰ Terlambat: *{$daysLate} hari*\n\n";
            $message .= "Segera kembalikan buku ke perpustakaan untuk menghindari keterlambatan lebih besar.\n\n";
            $message .= 'Terima kasih!';

            $success = $whatsApp->sendMessage($member, $member->whatsapp, $message);

            if ($success) {
                $this->info("✓ Reminder overdue dikirim ke {$member->name}");
                $sent++;
            } else {
                $this->error("✗ Gagal mengirim ke {$member->name}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Selesai. {$sent} terkirim, {$failed} gagal.");

        return Command::SUCCESS;
    }
}
