<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Member;
use App\Models\MemberAttendance;
use Exception;

class MemberAttendanceService
{
    /**
     * Record member attendance when they scan QR at library kiosk
     * If member already has an active attendance, return existing one
     */
    public function recordAttendance(Member $member): MemberAttendance
    {
        // Check if member already has active attendance
        $existing = MemberAttendance::active()
            ->where('member_id', $member->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Mark all previous attendances as "left"
        MemberAttendance::active()
            ->where('member_id', $member->id)
            ->update(['status' => 'left']);

        // Create new attendance
        return MemberAttendance::create([
            'member_id' => $member->id,
            'scanned_at' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * Get current active attendance for a member
     */
    public function getActiveAttendance(Member $member): ?MemberAttendance
    {
        return MemberAttendance::active()
            ->where('member_id', $member->id)
            ->first();
    }

    /**
     * Get all members currently at the library
     */
    public function getCurrentMembers(): array
    {
        return MemberAttendance::active()
            ->with('member')
            ->orderBy('scanned_at', 'desc')
            ->get()
            ->map(fn ($attendance) => [
                'id' => $attendance->id,
                'member_id' => $attendance->member_id,
                'name' => $attendance->member->name,
                'member_code' => $attendance->member->member_code,
                'photo' => $attendance->member->qr_code_url,
                'scanned_at' => $attendance->scanned_at->format('H:i:s'),
                'class' => $attendance->member->class,
                'major' => $attendance->member->major,
            ])
            ->toArray();
    }

    /**
     * Mark member as left (finished transaction)
     */
    public function markAsLeft(Member $member): void
    {
        MemberAttendance::active()
            ->where('member_id', $member->id)
            ->update(['status' => 'left']);
    }
}
