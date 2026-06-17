<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BorrowingDetailStatus;
use App\Enums\BorrowingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrowing extends Model
{
    protected $fillable = [
        'transaction_code',
        'member_id',
        'user_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'status' => BorrowingStatus::class,
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function bookReturn(): HasOne
    {
        return $this->hasOne(BookReturn::class);
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    /**
     * Buku yang belum dikembalikan
     */
    public function activeDetails(): HasMany
    {
        return $this->details()->where('status', BorrowingDetailStatus::Borrowed->value);
    }

    /**
     * Buku yang sudah dikembalikan
     */
    public function returnedDetails(): HasMany
    {
        return $this->details()->where('status', BorrowingDetailStatus::Returned->value);
    }

    public function isOverdue(): bool
    {
        if (! $this->due_date) {
            return false;
        }

        return in_array($this->status, [BorrowingStatus::Active, BorrowingStatus::Late], true)
            && $this->hasUnreturnedBooks()
            && $this->daysUntilDue() < 0;
    }

    public function daysUntilDue(): int
    {
        if (! $this->due_date) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->due_date->copy()->startOfDay(), false);
    }

    public function daysOverdue(): int
    {
        if (! $this->due_date) {
            return 0;
        }

        $referenceDate = ($this->return_date ?? now())->copy()->startOfDay();

        return max(0, (int) $this->due_date->copy()->startOfDay()->diffInDays($referenceDate, false));
    }

    public function dueCountdownLabel(): string
    {
        $days = $this->daysUntilDue();

        if ($days < 0) {
            return 'Terlambat '.abs($days).' hari';
        }

        if ($days === 0) {
            return 'Jatuh tempo hari ini';
        }

        return $days.' hari lagi';
    }

    public function hasUnreturnedBooks(): bool
    {
        return $this->details()->where('status', BorrowingDetailStatus::Borrowed->value)->exists();
    }

    public function isFullyReturned(): bool
    {
        return $this->details()->where('status', BorrowingDetailStatus::Borrowed->value)->count() === 0;
    }

    /**
     * QR code return — format: RET-{transaction_code}
     * Printed on member's return slip, scanned by admin at return counter
     */
    public function getReturnCodeAttribute(): string
    {
        return 'RET-' . $this->transaction_code;
    }
}
