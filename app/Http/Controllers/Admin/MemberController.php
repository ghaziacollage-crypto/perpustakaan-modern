<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\MemberStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Members\StoreMemberRequest;
use App\Http\Requests\Members\UpdateMemberRequest;
use App\Models\Member;
use App\Services\AuditService;
use App\Services\MemberPhotoService;
use App\Services\MemberQrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly MemberQrCodeService $memberQr,
        private readonly MemberPhotoService $memberPhoto
    ) {}

    public function index(Request $request): View
    {
        $query = Member::query();

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('member_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('nis_nim', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $members = $query->latest()->paginate(10)->withQueryString();

        return view('admin.members.index', compact('members', 'status'));
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->memberPhoto->upload($request->file('photo'));
        }

        $member = Member::create($data);
        $this->memberQr->generate($member);
        $this->audit->logCreate($member);

        return redirect()->route('admin.members.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $oldValues = $member->getAttributes();
        $data = $request->validated();

        // Handle photo upload / remove
        if (isset($data['remove_photo']) && $data['remove_photo']) {
            $this->memberPhoto->delete($member->photo);
            $data['photo'] = null;
            unset($data['remove_photo']);
        } elseif ($request->hasFile('photo')) {
            $this->memberPhoto->delete($member->photo);
            $data['photo'] = $this->memberPhoto->upload($request->file('photo'));
        }

        $member->update($data);
        $this->audit->logUpdate($member, $oldValues, $member->getAttributes());

        return redirect()->route('admin.members.index')->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->audit->logDelete($member);
        $this->memberQr->delete($member->qr_code);
        $this->memberPhoto->delete($member->photo);
        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Anggota berhasil dihapus.');
    }

    public function show(Member $member): View
    {
        $member->loadCount(['borrowings', 'activeBorrowings', 'fines']);

        $recentBorrowings = $member->borrowings()
            ->with(['details.book'])
            ->latest()
            ->take(5)
            ->get();

        $unpaidFines = $member->fines()
            ->where('status', 'unpaid')
            ->latest()
            ->get();

        return view('admin.members.show', compact('member', 'recentBorrowings', 'unpaidFines'));
    }

    public function printCard(Member $member): View
    {
        return view('admin.members.print-card', compact('member'));
    }

    public function regenerateQr(Member $member): RedirectResponse
    {
        $this->memberQr->regenerate($member);

        return redirect()->back()->with('success', 'QR Code anggota berhasil digenerate ulang.');
    }

    public function bulkRegenerateQr(): RedirectResponse
    {
        $count = $this->memberQr->regenerateAll();

        return redirect()->route('admin.members.index')
            ->with('success', "QR Code berhasil disinkronisasi untuk {$count} anggota.");
    }

    public function bulkQrPage(): View
    {
        $totalMembers = Member::count();
        $membersWithQr = Member::whereNotNull('qr_code')->whereRaw('qr_code != ""')->count();
        $membersWithoutQr = Member::whereNull('qr_code')->orWhereRaw('qr_code = ""')->count();

        return view('admin.members.bulk-qr', compact(
            'totalMembers',
            'membersWithQr',
            'membersWithoutQr'
        ));
    }

    /**
     * POST /admin/members/{member}/approve
     * Approve a pending member registration
     */
    public function approve(Member $member): RedirectResponse
    {
        if ($member->status !== MemberStatus::Pending) {
            return redirect()->route('admin.members.index')
                ->with('error', 'Anggota sudah diproses.');
        }

        $member->update(['status' => MemberStatus::Active]);
        $this->memberQr->generate($member);
        $this->audit->log($member, 'approved', [
            'status' => ['old' => 'pending', 'new' => 'active'],
        ]);

        return redirect()->route('admin.members.index')
            ->with('success', "Anggota {$member->name} berhasil disetujui. QR Code telah digenerate.");
    }

    /**
     * POST /admin/members/{member}/reject
     * Reject and delete a pending member registration
     */
    public function reject(Member $member): RedirectResponse
    {
        if ($member->status !== MemberStatus::Pending) {
            return redirect()->route('admin.members.index')
                ->with('error', 'Anggota sudah diproses.');
        }

        $name = $member->name;

        // Delete photo if exists
        if ($member->photo) {
            $this->memberPhoto->delete($member->photo);
        }

        // QR code should be null for pending, but clean up anyway
        if ($member->qr_code) {
            $this->memberQr->delete($member->qr_code);
        }

        $this->audit->logCustom($member, 'rejected', [
            'rejected' => ['name' => $name, 'nis_nim' => $member->nis_nim],
        ]);

        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('success', "Pendaftaran {$name} ditolak dan dihapus.");
    }

    public function lookupByCode(Request $request): JsonResponse
    {
        $member = Member::where('member_code', $request->get('code'))->first();

        if (! $member) {
            return response()->json(['error' => 'Anggota tidak ditemukan'], 404);
        }

        if (! $member->isActive()) {
            return response()->json(['error' => 'Anggota tidak aktif'], 403);
        }

        return response()->json([
            'id' => $member->id,
            'member_code' => $member->member_code,
            'name' => $member->name,
            'nis_nim' => $member->nis_nim,
            'class' => $member->class,
            'active_borrowings' => $member->activeBorrowings()->count(),
        ]);
    }
}
