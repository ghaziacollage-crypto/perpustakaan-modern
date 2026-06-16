<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BorrowingDetailStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingDetail extends Model
{
    protected $fillable = [
        'borrowing_id',
        'book_id',
        'status',
        'returned_at',
        'condition',
    ];

    protected $casts = [
        'status' => BorrowingDetailStatus::class,
        'returned_at' => 'date',
    ];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Scope: hanya buku yang belum dikembalikan
     */
    public function scopeNotReturned($query)
    {
        return $query->where('status', BorrowingDetailStatus::Borrowed->value);
    }

    /**
     * Scope: buku yang sudah dikembalikan
     */
    public function scopeReturned($query)
    {
        return $query->where('status', BorrowingDetailStatus::Returned->value);
    }
}
