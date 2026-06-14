<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueReminders extends Command
{
    protected $signature = 'reminder:send {--days=3}';

    protected $description = 'Kirim reminder WhatsApp ke anggota yang buku akan jatuh tempo';

    public function handle(WhatsAppService $whatsApp): int
    {
        $days = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($days)->toDateString();

        $borrowings = Borrowing::with(['member', 'details.book'])
            ->where('status', BorrowingStatus::Active->value)
            ->whereDate('due_date', $targetDate)
            ->whereDoesntHave('bookReturn')
            ->get();

        if ($borrowings->isEmpty()) {
            $this->info("Tidak ada peminjaman yang akan jatuh tempo dalam {$days} hari.");

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
            $dueDateFormatted = Carbon::parse($borrowing->due_date)->translatedFormat('d F Y');

            $message = "Halo {$member->name} 👋,\n\n";
            $message .= "Pengingat: Buku yang Anda pinjam akan jatuh tempo dalam *{$days} hari*.\n\n";
            $message .= "📚 Buku: {$bookTitles}\n";
            $message .= "📅 Jatuh Tempo: {$dueDateFormatted}\n\n";
            $message .= "Harap kembalikan tepat waktu agar tidak terkena keterlambatan.\n\n";
            $message .= 'Terima kasih! 🙏';

            $success = $whatsApp->sendMessage($member, $member->whatsapp, $message);

            if ($success) {
                $this->info("✓ Reminder dikirim ke {$member->name} ({$member->whatsapp})");
                $sent++;
            } else {
                $this->error("✗ Gagal mengirim ke {$member->name} ({$member->whatsapp})");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Selesai. {$sent} terkirim, {$failed} gagal.");

        return Command::SUCCESS;
    }
}
