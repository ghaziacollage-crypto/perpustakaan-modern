<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BorrowingDetailStatus;
use App\Enums\BorrowingStatus;
use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    public const MAX_BORROWINGS = 3;

    protected $fillable = [
        'member_code',
        'name',
        'nis_nim',
        'class',
        'major',
        'address',
        'whatsapp',
        'email',
        'photo',
        'qr_code',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => MemberStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(MemberAttendance::class);
    }

    /**
     * Pinjaman aktif = borrowing dengan status Active/Late
     * dan masih punya borrowing_details yang belum dikembalikan
     */
    public function activeBorrowings(): HasMany
    {
        return $this->borrowings()
            ->whereIn('status', [BorrowingStatus::Active, BorrowingStatus::Late])
            ->whereHas('details', fn ($q) => $q->where('status', BorrowingDetailStatus::Borrowed));
    }

    /**
     * Total buku yang sedang dipinjam (belum dikembalikan)
     */
    public function getActiveBorrowingsCountAttribute(): int
    {
        return $this->activeBorrowings()
            ->withCount(['details' => fn ($q) => $q->where('status', BorrowingDetailStatus::Borrowed)])
            ->get()
            ->sum('details_count');
    }

    /**
     * Sisa slot peminjaman
     */
    public function getRemainingSlotsAttribute(): int
    {
        return max(0, self::MAX_BORROWINGS - $this->active_borrowings_count);
    }

    /**
     * Cek apakah member masih boleh meminjam
     */
    public function canBorrow(): bool
    {
        return $this->status === MemberStatus::Active
            && $this->remaining_slots > 0;
    }

    /**
     * Apakah member aktif
     */
    public function isActive(): bool
    {
        return $this->status === MemberStatus::Active;
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_code ? asset('storage/'.$this->qr_code) : null;
    }
}
