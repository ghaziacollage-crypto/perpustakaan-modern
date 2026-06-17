<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookStatus;
use App\Enums\BookCondition;
use App\Enums\BorrowingDetailStatus;
use App\Enums\BorrowingStatus;
use App\Models\Book;
use App\Models\BookReturn;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingService
{
    public const DEFAULT_LOAN_DAYS = 7;

    public const MAX_BORROWINGS_PER_MEMBER = 3;

    public function __construct(
        private readonly WhatsAppService $whatsApp,
    ) {}

    /**
     * Create new borrowing transaction
     *
     * @throws ValidationException
     */
    public function createBorrowing(Member $member, array $bookIds, ?Carbon $dueDate = null, ?string $notes = null): Borrowing
    {
        return $this->createBorrowings($member, $bookIds, $dueDate, $notes)->first();
    }

    /**
     * Create one borrowing record for each selected book.
     *
     * @return Collection<int, Borrowing>
     *
     * @throws ValidationException
     */
    public function createBorrowings(Member $member, array $bookIds, ?Carbon $dueDate = null, ?string $notes = null): Collection
    {
        // Validate member first
        $this->validateMember($member);

        // Normalize and dedupe book IDs
        $bookIds = array_values(array_unique($bookIds));

        if (empty($bookIds)) {
            throw ValidationException::withMessages(['book_ids' => 'Pilih setidaknya satu buku.']);
        }

        // Check remaining slots
        $remainingSlots = $this->getRemainingSlots($member);
        if (count($bookIds) > $remainingSlots) {
            throw ValidationException::withMessages([
                'book_ids' => "Sisa slot peminjaman hanya {$remainingSlots} buku. Kurangi jumlah buku yang dipinjam.",
            ]);
        }

        return DB::transaction(function () use ($member, $bookIds, $dueDate, $notes) {
            // Lock books for update to prevent race conditions
            $books = Book::query()
                ->whereIn('id', $bookIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($books->count() !== count($bookIds)) {
                $notFoundIds = array_diff($bookIds, $books->pluck('id')->toArray());
                throw ValidationException::withMessages([
                    'book_ids' => 'Sebagian buku tidak ditemukan (ID: '.implode(', ', $notFoundIds).').',
                ]);
            }

            // Validate each book
            foreach ($books as $book) {
                $this->validateBook($book);
            }

            // Final check: total borrowed/reserved after this transaction
            $newTotal = $this->getOutstandingBorrowingsCount($member) + count($bookIds);
            if ($newTotal > self::MAX_BORROWINGS_PER_MEMBER) {
                throw ValidationException::withMessages([
                    'book_ids' => "Melebihi batas maksimal peminjaman. Sisa slot: {$remainingSlots} buku.",
                ]);
            }

            $created = collect();
            $loanDate = now()->toDateString();
            $resolvedDueDate = ($dueDate ?? now()->addDays($this->getLoanDuration()))->toDateString();

            foreach ($books as $book) {
                $borrowing = Borrowing::create([
                    'transaction_code' => $this->generateTransactionCode(),
                    'member_id' => $member->id,
                    'user_id' => auth()->id(),
                    'loan_date' => $loanDate,
                    'due_date' => $resolvedDueDate,
                    'status' => BorrowingStatus::Active,
                    'notes' => $notes,
                ]);

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $book->id,
                    'status' => BorrowingDetailStatus::Borrowed,
                ]);

                // Decrement stock
                $book->decrement('stock');
                if ($book->stock <= 0) {
                    $book->update(['status' => BookStatus::Unavailable]);
                }

                $created->push($borrowing->load(['member', 'details.book']));
            }

            $created->each(fn (Borrowing $borrowing) => $this->sendBorrowedNotification($member, $borrowing));

            return $created;
        });
    }

    /**
     * Return books from a borrowing
     */
    public function returnBorrowing(Borrowing $borrowing, array $data): Borrowing
    {
        return DB::transaction(function () use ($borrowing, $data) {
            $borrowing->load(['details.book', 'member']);

            $returnDate = Carbon::parse($data['return_date'] ?? now());
            $detailIds = empty($data['detail_ids'])
                ? $borrowing->activeDetails()->pluck('id')->toArray()
                : $data['detail_ids'];
            $condition = $data['condition'] ?? null;
            $notes = $data['notes'] ?? null;
            $isLost = $this->isLostReturnCondition($condition);

            // Check which detail IDs are still borrowable
            $validDetailIds = $borrowing->activeDetails()
                ->whereIn('id', $detailIds)
                ->pluck('id')
                ->toArray();

            if (empty($validDetailIds)) {
                throw new Exception('Semua buku sudah dikembalikan.');
            }

            // Update details as returned
            $borrowing->details()
                ->whereIn('id', $validDetailIds)
                ->update([
                    'status' => BorrowingDetailStatus::Returned,
                    'returned_at' => $returnDate->toDateString(),
                    'condition' => $condition,
                ]);

            // Restore stock for returned books, except books marked as lost.
            $returnedDetails = $borrowing->details()->whereIn('id', $validDetailIds)->get();
            foreach ($returnedDetails as $detail) {
                if ($isLost) {
                    $detail->book->update([
                        'status' => BookStatus::Unavailable,
                        'kondisi' => BookCondition::Hilang,
                    ]);

                    continue;
                }

                $detail->book->increment('stock');
                $detail->book->refresh();

                if ($detail->book->stock > 0) {
                    $detail->book->update([
                        'status' => BookStatus::Available,
                        'kondisi' => $this->isDamagedReturnCondition($condition)
                            ? BookCondition::Rusak
                            : BookCondition::Normal,
                    ]);
                }
            }

            // Check if all details are returned
            $allReturned = $borrowing->activeDetails()->count() === 0;

            if ($allReturned) {
                // Create book return record
                BookReturn::create([
                    'borrowing_id' => $borrowing->id,
                    'return_date' => $returnDate->toDateString(),
                    'condition' => $condition,
                    'notes' => $notes,
                ]);

                // Update borrowing status
                $borrowing->update([
                    'return_date' => $returnDate->toDateString(),
                    'status' => $returnDate->copy()->startOfDay()->gt($borrowing->due_date->copy()->startOfDay()) ? BorrowingStatus::Late : BorrowingStatus::Returned,
                ]);

                // No fines - library does not charge penalties
            }

            return $borrowing->refresh();
        });
    }

    private function isLostReturnCondition(?string $condition): bool
    {
        return in_array(strtolower(trim((string) $condition)), ['lost', 'hilang', 'buku hilang'], true);
    }

    private function isDamagedReturnCondition(?string $condition): bool
    {
        $normalized = strtolower(trim((string) $condition));

        return str_contains($normalized, 'damaged') || str_contains($normalized, 'rusak');
    }

    /**
     * Send reminder to member
     */
    public function sendReminder(Borrowing $borrowing): bool
    {
        return $this->sendReminderWithResult($borrowing)['success'];
    }

    public function sendReminderWithResult(Borrowing $borrowing): array
    {
        $borrowing->loadMissing(['member', 'details.book']);
        $member = $borrowing->member;

        if (! $member?->whatsapp) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp anggota belum diisi.',
            ];
        }

        $daysLeft = max(0, $borrowing->daysUntilDue());
        $overdueDays = $borrowing->daysOverdue();

        if ($overdueDays > 0) {
            $message = "🔔 Halo {$member->name}, buku Anda terlambat {$overdueDays} hari!\n";
            $message .= "Kode: {$borrowing->transaction_code}\n";
            $message .= 'Buku: '.$borrowing->details->pluck('book.title')->filter()->implode(', ')."\n";
            $message .= 'Segera kembalikan ke perpustakaan.';
        } else {
            $message = $daysLeft === 0
                ? "📚 Halo {$member->name}, buku jatuh tempo hari ini.\n"
                : "📚 Halo {$member->name}, buku akan jatuh tempo dalam {$daysLeft} hari.\n";
            $message .= "Kode: {$borrowing->transaction_code}\n";
            $message .= 'Buku: '.$borrowing->details->pluck('book.title')->filter()->implode(', ')."\n";
            $message .= " Jatuh tempo: {$borrowing->due_date->format('d M Y')}";
        }

        return $this->whatsApp->sendMessageWithResult($member, $member->whatsapp, $message);
    }

    // ── Validasi ──────────────────────────────────────────────────────────────

    /**
     * Validate member can borrow
     *
     * @throws ValidationException
     */
    public function validateMember(Member $member): void
    {
        if ($member->status->value !== 'active') {
            throw ValidationException::withMessages(['member_id' => 'Anggota tidak aktif.']);
        }

        if (! $member->canBorrow()) {
            $remainingSlots = $member->remaining_slots;
            throw ValidationException::withMessages([
                'member_id' => $remainingSlots <= 0
                    ? 'Anggota sudah mencapai batas maksimal peminjaman (3 buku).'
                    : "Anggota hanya boleh meminjam {$remainingSlots} buku lagi.",
            ]);
        }
    }

    /**
     * Validate single book
     *
     * @throws ValidationException
     */
    public function validateBook(Book $book): void
    {
        if ($book->stock <= 0) {
            throw ValidationException::withMessages([
                'book_ids' => "Stok buku \"{$book->title}\" habis.",
            ]);
        }

        // Check if book is currently borrowed by someone
        $currentlyBorrowed = BorrowingDetail::where('book_id', $book->id)
            ->where('status', BorrowingDetailStatus::Borrowed->value)
            ->exists();

        if ($currentlyBorrowed) {
            throw ValidationException::withMessages([
                'book_ids' => "Buku \"{$book->title}\" sedang dipinjam anggota lain.",
            ]);
        }
    }

    /**
     * Validate a single book by code (for API lookup)
     *
     * @throws ValidationException
     */
    public function validateBookByCode(string $bookCode, int $requestedSlots = 1): array
    {
        $book = Book::with('category')->where('book_code', $bookCode)->first();

        if (! $book) {
            return ['success' => false, 'error' => 'Buku tidak ditemukan.'];
        }

        if ($book->stock <= 0) {
            return ['success' => false, 'error' => "Stok buku \"{$book->title}\" habis."];
        }

        $currentlyBorrowed = BorrowingDetail::where('book_id', $book->id)
            ->where('status', BorrowingDetailStatus::Borrowed->value)
            ->exists();

        if ($currentlyBorrowed) {
            return ['success' => false, 'error' => "Buku \"{$book->title}\" sedang dipinjam."];
        }

        return [
            'success' => true,
            'book' => [
                'id' => $book->id,
                'book_code' => $book->book_code,
                'title' => $book->title,
                'author' => $book->author,
                'category' => $book->category?->name,
                'stock' => $book->stock,
                'cover' => $book->cover_url,
            ],
        ];
    }

    /**
     * Validate a member by code (for API lookup)
     */
    public function validateMemberByCode(string $memberCode): array
    {
        $member = Member::where('member_code', $memberCode)->first();

        if (! $member) {
            return ['success' => false, 'error' => 'Anggota tidak ditemukan.'];
        }

        if ($member->status->value !== 'active') {
            return ['success' => false, 'error' => 'Anggota tidak aktif.'];
        }

        $activeCount = $this->getActiveBorrowingsCount($member);
        $remainingSlots = $this->getRemainingSlots($member);
        $activeBorrowings = $member->activeBorrowings()->with(['details' => fn ($q) => $q->with('book')->where('status', BorrowingDetailStatus::Borrowed->value)])->get();

        return [
            'success' => true,
            'member' => [
                'id' => $member->id,
                'member_code' => $member->member_code,
                'name' => $member->name,
                'nis_nim' => $member->nis_nim,
                'photo' => $member->photo ? asset('storage/'.$member->photo) : null,
                'status' => $member->status->value,
                'active_borrowings_count' => $activeCount,
                'remaining_slots' => $remainingSlots,
                'active_borrowings' => $activeBorrowings->map(fn ($b) => [
                    'id' => $b->id,
                    'transaction_code' => $b->transaction_code,
                    'loan_date' => $b->loan_date->format('d M Y'),
                    'due_date' => $b->due_date->format('d M Y'),
                    'is_overdue' => $b->isOverdue(),
                    'books' => $b->details->map(fn ($d) => [
                        'id' => $d->id,
                        'title' => $d->book->title,
                        'book_code' => $d->book->book_code,
                        'cover' => $d->book->cover_url,
                        'status' => $d->status->value,
                    ]),
                ])->toArray(),
            ],
        ];
    }

    // ── Query Helpers ─────────────────────────────────────────────────────────

    public function getActiveBorrowingsCount(Member $member): int
    {
        return (int) BorrowingDetail::whereHas('borrowing', fn ($q) => $q
            ->where('member_id', $member->id)
            ->whereIn('status', [BorrowingStatus::Active->value, BorrowingStatus::Late->value]))
            ->where('status', BorrowingDetailStatus::Borrowed)
            ->count();
    }

    public function getOutstandingBorrowingsCount(Member $member): int
    {
        return (int) BorrowingDetail::whereHas('borrowing', fn ($q) => $q
            ->where('member_id', $member->id)
            ->whereIn('status', [
                BorrowingStatus::Pending->value,
                BorrowingStatus::Active->value,
                BorrowingStatus::Late->value,
            ]))
            ->where('status', BorrowingDetailStatus::Borrowed)
            ->count();
    }

    public function getRemainingSlots(Member $member): int
    {
        return max(0, self::MAX_BORROWINGS_PER_MEMBER - $this->getOutstandingBorrowingsCount($member));
    }

    public function getLoanDuration(): int
    {
        return (int) Setting::getValue('loan_duration_days', self::DEFAULT_LOAN_DAYS);
    }

    public function getDefaultDueDate(): Carbon
    {
        return now()->addDays($this->getLoanDuration());
    }

    // ── Transaction Code Lookup ───────────────────────────────────────────────

    /**
     * Find borrowing by transaction code (for return scan)
     */
    public function findByTransactionCode(string $code): array
    {
        $borrowing = Borrowing::with(['member', 'details.book'])
            ->where('transaction_code', $code)
            ->first();

        if (! $borrowing) {
            return ['success' => false, 'error' => 'Transaksi tidak ditemukan.'];
        }

        if (! $borrowing->details->contains(fn (BorrowingDetail $detail) => $detail->status === BorrowingDetailStatus::Borrowed)) {
            return ['success' => false, 'error' => 'Semua buku pada transaksi ini sudah dikembalikan.'];
        }

        $isOverdue = $borrowing->isOverdue();
        $daysLeft = $borrowing->daysUntilDue();

        return [
            'success' => true,
            'data' => [
                'id' => $borrowing->id,
                'transaction_code' => $borrowing->transaction_code,
                'loan_date' => $borrowing->loan_date->format('Y-m-d'),
                'due_date' => $borrowing->due_date->format('Y-m-d'),
                'return_date' => $borrowing->return_date?->format('Y-m-d'),
                'status' => $borrowing->status->value,
                'is_overdue' => $isOverdue,
                'days_left' => $daysLeft,
                'member' => [
                    'id' => $borrowing->member->id,
                    'member_code' => $borrowing->member->member_code,
                    'name' => $borrowing->member->name,
                    'nis_nim' => $borrowing->member->nis_nim,
                    'class' => $borrowing->member->class,
                    'photo' => $borrowing->member->photo ? asset('storage/'.$borrowing->member->photo) : null,
                ],
                'books' => $borrowing->details->map(fn ($d) => [
                    'id' => $d->id,
                    'title' => $d->book->title,
                    'author' => $d->book->author,
                    'book_code' => $d->book->book_code,
                    'cover' => $d->book->cover ? asset('storage/'.$d->book->cover) : null,
                    'status' => $d->status->value,
                ])->toArray(),
            ],
        ];
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function generateTransactionCode(): string
    {
        $date = now()->format('Ymd');
        $count = Borrowing::whereDate('created_at', now()->toDateString())->count() + 1;

        return 'TRX-'.$date.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function sendBorrowedNotification(Member $member, Borrowing $borrowing): void
    {
        if (! $member->whatsapp) {
            return;
        }

        $message = "📚 Hai {$member->name}!\n";
        $message .= "Peminjaman berhasil dicatat.\n\n";
        $message .= "📋 Kode: {$borrowing->transaction_code}\n";
        $message .= "📅 Pinjam: {$borrowing->loan_date->format('d M Y')}\n";
        $message .= "⏰ Kembali: {$borrowing->due_date->format('d M Y')}\n\n";
        $message .= "📕 Buku: {$borrowing->details->count()} item\n";
        $message .= 'Silakan cek ke perpustakaan untuk info lebih lanjut.';

        $this->whatsApp->sendMessage($member, $member->whatsapp, $message);
    }

    // ── Pending Borrowing (for scan workflow) ────────────────────────────────

    /**
     * Create a pending borrowing request (after member + books scanned at kiosk)
     * Books are NOT deducted from stock yet — only after admin approval
     */
    public function createPending(Member $member, array $bookIds, ?string $notes = null): Borrowing
    {
        return $this->createPendingBorrowings($member, $bookIds, $notes)->first();
    }

    /**
     * Create one pending borrowing request for each selected book.
     *
     * @return Collection<int, Borrowing>
     *
     * @throws ValidationException
     */
    public function createPendingBorrowings(Member $member, array $bookIds, ?string $notes = null): Collection
    {
        $this->validateMember($member);

        $bookIds = array_values(array_unique($bookIds));

        if (empty($bookIds)) {
            throw ValidationException::withMessages(['book_ids' => 'Pilih setidaknya satu buku.']);
        }

        $remainingSlots = $this->getRemainingSlots($member);
        if (count($bookIds) > $remainingSlots) {
            throw ValidationException::withMessages([
                'book_ids' => "Sisa slot peminjaman hanya {$remainingSlots} buku. Kurangi jumlah buku yang dipinjam.",
            ]);
        }

        return DB::transaction(function () use ($member, $bookIds, $notes) {
            $books = Book::query()
                ->whereIn('id', $bookIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($books->count() !== count($bookIds)) {
                $notFoundIds = array_diff($bookIds, $books->pluck('id')->toArray());
                throw ValidationException::withMessages([
                    'book_ids' => 'Sebagian buku tidak ditemukan (ID: '.implode(', ', $notFoundIds).').',
                ]);
            }

            foreach ($books as $book) {
                $this->validateBook($book);
            }

            $remainingSlots = $this->getRemainingSlots($member);
            $newTotal = $this->getOutstandingBorrowingsCount($member) + count($bookIds);
            if ($newTotal > self::MAX_BORROWINGS_PER_MEMBER) {
                throw ValidationException::withMessages([
                    'book_ids' => "Melebihi batas maksimal peminjaman. Sisa slot: {$remainingSlots} buku.",
                ]);
            }

            $created = collect();
            $loanDate = now()->toDateString();
            $dueDate = now()->addDays($this->getLoanDuration())->toDateString();

            foreach ($books as $book) {
                $borrowing = Borrowing::create([
                    'transaction_code' => $this->generateTransactionCode(),
                    'member_id' => $member->id,
                    'user_id' => null,
                    'loan_date' => $loanDate,
                    'due_date' => $dueDate,
                    'status' => BorrowingStatus::Pending,
                    'notes' => $notes,
                ]);

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $book->id,
                    'status' => BorrowingDetailStatus::Borrowed,
                ]);

                $created->push($borrowing->load(['member', 'details.book']));
            }

            return $created;
        });
    }
    /**
     * Approve a pending borrowing — changes status to Active and decrements stock
     */
    public function approve(Borrowing $borrowing, int $approvedByUserId): Borrowing
    {
        if ($borrowing->status !== BorrowingStatus::Pending) {
            throw new Exception('Hanya peminjaman dengan status Pending yang bisa disetujui.');
        }

        return DB::transaction(function () use ($borrowing, $approvedByUserId) {
            $borrowing->load(['details.book']);

            // Update borrowing
            $borrowing->update([
                'user_id' => $approvedByUserId,
                'status' => BorrowingStatus::Active,
            ]);

            // Decrement stock for each book
            foreach ($borrowing->details as $detail) {
                $detail->book->decrement('stock');
                if ($detail->book->stock <= 0) {
                    $detail->book->update(['status' => BookStatus::Unavailable]);
                }
            }

            // Send notification
            $this->sendBorrowedNotification($borrowing->member, $borrowing->refresh());

            return $borrowing->refresh();
        });
    }

    /**
     * Reject a pending borrowing — removes the borrowing record
     */
    public function reject(Borrowing $borrowing): void
    {
        if ($borrowing->status !== BorrowingStatus::Pending) {
            throw new Exception('Hanya peminjaman dengan status Pending yang bisa ditolak.');
        }

        DB::transaction(function () use ($borrowing) {
            // Load member untuk notifikasi
            $borrowing->load('member');

            // Send rejection notification
            $this->sendRejectionNotification($borrowing->member, $borrowing);

            // Delete borrowing details first
            $borrowing->details()->delete();
            // Delete borrowing
            $borrowing->delete();
        });
    }

    private function sendRejectionNotification(Member $member, Borrowing $borrowing): void
    {
        if (! $member->whatsapp) {
            return;
        }

        $message = "📚 Hai {$member->name}!\n";
        $message .= "Maaf, pengajuan peminjaman Anda ditolak oleh admin.\n\n";
        $message .= "📋 Kode: {$borrowing->transaction_code}\n";
        $message .= "📅 Tanggal pengajuan: {$borrowing->created_at->format('d M Y H:i')}\n\n";
        $message .= "Silakan hubungi perpustakaan untuk informasi lebih lanjut.";

        $this->whatsApp->sendMessage($member, $member->whatsapp, $message);
    }
}
