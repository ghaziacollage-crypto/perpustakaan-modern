@extends('layouts.app')

@section('title', 'Keterlambatan')
@section('page-title', 'Keterlambatan')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Transaksi</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Keterlambatan</li>
</ul>
@endsection

@push('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

{{-- Summary Cards --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-yellow) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">⏰</div>
                <div>
                    <div class="stat-label">BELUM LUNAS</div>
                    <div class="stat-value" style="color:var(--comic-yellow);">
                        Rp {{ number_format((float) $totalUnpaid, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-green) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">✅</div>
                <div>
                    <div class="stat-label">SUDAH LUNAS</div>
                    <div class="stat-value" style="color:var(--comic-green);">
                        Rp {{ number_format((float) $totalPaid, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-blue) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">💰</div>
                <div>
                    <div class="stat-label">TOTAL KETERLAMBATAN</div>
                    <div class="stat-value" style="color:var(--comic-blue);">
                        Rp {{ number_format((float) $totalAll, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">💰 MANAJEMEN KETERLAMBATAN</span>
        </div>
        <div class="card-toolbar d-flex align-items-center gap-2">
            <a href="{{ route('admin.fines.index') }}"
                class="btn-filter {{ !request('status') ? 'active' : '' }}">
                📋 Semua
            </a>
            <a href="{{ route('admin.fines.index', ['status' => 'unpaid']) }}"
                class="btn-filter {{ request('status') === 'unpaid' ? 'active' : '' }}">
                ⏰ Belum Lunas
            </a>
            <a href="{{ route('admin.fines.index', ['status' => 'paid']) }}"
                class="btn-filter {{ request('status') === 'paid' ? 'active' : '' }}">
                ✅ Lunas
            </a>
        </div>
    </div>

    <div class="card-body py-4 px-4">

        {{-- Table --}}
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:50px;">#</th>
                        <th style="min-width:150px;">Anggota</th>
                        <th style="min-width:160px;">Buku</th>
                        <th style="min-width:70px;">Terlambat</th>
                        <th style="min-width:110px;">Jumlah</th>
                        <th style="min-width:80px;">Status</th>
                        <th style="min-width:110px;">Tanggal Lunas</th>
                        <th class="text-end" style="min-width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($fines as $index => $fine)
                    <tr>
                        <td>
                            <span class="fw-bold" style="color:var(--comic-orange); font-family:'Bangers',cursive; font-size:1rem;">
                                {{ str_pad((string)($fines->firstItem() + $index), 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="symbol symbol-35px flex-shrink-0">
                                    <div class="symbol-label fs-5 fw-bold"
                                        style="background:var(--comic-cream); color:var(--comic-dark); border:2px solid var(--comic-dark);">
                                        {{ strtoupper(substr($fine->member->name ?? '?', 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size:0.85rem;">{{ $fine->member->name ?? '-' }}</span>
                                    <span class="text-muted" style="font-size:0.72rem;">{{ $fine->member->member_code ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <ul class="mb-0 ps-3" style="list-style:none; padding:0;">
                                @foreach($fine->borrowing->details->take(2) as $detail)
                                    <li style="font-size:0.8rem; color:#555; font-weight:700;">
                                        📕 {{ Str::limit($detail->book->title ?? '-', 25) }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <span class="fw-bold text-danger" style="font-size:0.9rem; font-family:'Bangers',cursive;">
                                {{ $fine->days_late }}<span style="font-size:0.65rem; font-weight:700; color:#aaa;">hari</span>
                            </span>
                        </td>
                        <td>
                            <span class="fw-black" style="color:var(--comic-red); font-family:'Bangers',cursive; font-size:1.1rem;">
                                Rp {{ number_format((float) $fine->total_amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @if($fine->status->value === 'paid')
                                <span class="badge badge-light-success" style="font-size:0.72rem;">✅ Lunas</span>
                            @else
                                <span class="badge badge-light-warning" style="font-size:0.72rem;">⏰ Belum</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size:0.82rem;">
                                {{ $fine->paid_at?->format('d M Y') ?: '-' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($fine->status->value === 'unpaid')
                                <form method="POST" action="{{ route('admin.fines.mark-as-paid', $fine) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-comic btn-paid" title="Tandai Lunas">
                                        <i class="ki-duotone ki-check-circle fs-4" style="color:#fff !important;"></i>
                                        LUNAS
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.fines.mark-as-unpaid', $fine) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-comic-delete btn-batal" title="Batalkan Lunas">
                                        <i class="ki-duotone ki-arrows-circle fs-4"></i>
                                        BATAL
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="comic-empty">
                                <span class="empty-emoji">🎉</span>
                                <div class="empty-title">TIDAK ADA DATA KETERLAMBATAN</div>
                                <div class="empty-sub">Semua keterlambatan sudah lunas atau belum ada keterlambatan</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('layouts.partials._pagination', ['paginator' => $fines])
    </div>
</div>
@endsection

@push('custom-js')
<style>
.btn-paid {
    background: var(--comic-green) !important;
    border-color: var(--comic-dark) !important;
    box-shadow: 3px 3px 0 var(--comic-dark) !important;
}
.btn-paid:hover {
    background: var(--comic-yellow) !important;
    color: var(--comic-dark) !important;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Paid confirmation
    document.querySelectorAll('.btn-paid').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var form = btn.closest('form');
            Swal.fire({
                title: 'Tandai lunas?',
                text: 'Keterlambatan akan ditandai sebagai sudah lunas.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Ya, lunas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#00C896',
            }).then(function (r) {
                if (r.isConfirmed) form.submit();
            });
        });
    });

    // Batal confirmation
    document.querySelectorAll('.btn-batal').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var form = btn.closest('form');
            Swal.fire({
                title: 'Batalkan?',
                text: 'Status keterlambatan akan dikembalikan ke belum lunas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#FF3366',
            }).then(function (r) {
                if (r.isConfirmed) form.submit();
            });
        });
    });
});
</script>
@endpush
