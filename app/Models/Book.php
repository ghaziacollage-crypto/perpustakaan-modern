<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'book_code',
        'isbn',
        'title',
        'category_id',
        'author',
        'publisher',
        'published_year',
        'year',
        'pages',
        'stock',
        'rack_location',
        'cover',
        'synopsis',
        'qr_code',
        'status',
        'kondisi',
    ];

    protected $casts = [
        'year' => 'integer',
        'published_year' => 'integer',
        'pages' => 'integer',
        'stock' => 'integer',
        'status' => BookStatus::class,
        'kondisi' => BookCondition::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === BookStatus::Available && $this->stock > 0;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover ? asset('storage/' . $this->cover) : null;
    }
}
