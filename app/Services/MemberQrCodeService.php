<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberQrCodeService
{
    public function generate(Member $member): Member
    {
        $fileName = 'qr/members/' . $member->member_code . '.svg';
        // SVG format — works tanpa imagick extension
        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($member->member_code);

        Storage::disk('public')->put($fileName, $qrSvg);
        $member->update(['qr_code' => $fileName]);

        return $member->refresh();
    }

    public function regenerate(Member $member): Member
    {
        $this->delete($member->qr_code);
        return $this->generate($member);
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function generateAll(): int
    {
        $count = 0;
        Member::chunk(100, function ($members) use (&$count) {
            foreach ($members as $member) {
                if (!$member->qr_code) {
                    $this->generate($member);
                    $count++;
                }
            }
        });

        return $count;
    }

    /**
     * Regenerate all member QR codes (bulk sync)
     * Deletes old file and generates new one for every member
     */
    public function regenerateAll(): int
    {
        $count = 0;
        Member::chunk(100, function ($members) use (&$count) {
            foreach ($members as $member) {
                $this->regenerate($member);
                $count++;
            }
        });

        return $count;
    }
}