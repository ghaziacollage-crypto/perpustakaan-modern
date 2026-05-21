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
        return $this->details()->where('status', BorrowingDetailStatus::Borrowed);
    }

    /**
     * Buku yang sudah dikembalikan
     */
    public function returnedDetails(): HasMany
    {
        return $this->details()->where('status', BorrowingDetailStatus::Returned);
    }

    public function isOverdue(): bool
    {
        if (! $this->due_date) {
            return false;
        }

        return $this->status === BorrowingStatus::Active && $this->due_date->lt(now());
    }

    public function daysOverdue(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return (int) $this->due_date->diffInDays(now());
    }

    public function hasUnreturnedBooks(): bool
    {
        return $this->details()->where('status', BorrowingDetailStatus::Borrowed)->exists();
    }

    public function isFullyReturned(): bool
    {
        return $this->details()->where('status', BorrowingDetailStatus::Borrowed)->count() === 0;
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
