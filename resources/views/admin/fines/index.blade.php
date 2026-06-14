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

@section('content')

{{-- Summary Cards --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-blue) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">📋</div>
                <div>
                    <div class="stat-label">TOTAL KETERLAMBATAN</div>
                    <div class="stat-value" style="color:var(--comic-blue);">
                        {{ number_format($fines->total()) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-yellow) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">⏰</div>
                <div>
                    <div class="stat-label">BELUM DIKEMBALIKAN</div>
                    <div class="stat-value" style="color:var(--comic-yellow);">
                        {{ number_format($fines->where('status', 'unpaid')->count()) }}
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
                    <div class="stat-label">SUDAH DIKEMBALIKAN</div>
                    <div class="stat-value" style="color:var(--comic-green);">
                        {{ number_format($fines->where('status', 'paid')->count()) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">⏰ RIWAYAT KETERLAMBATAN</span>
        </div>
        <div class="card-toolbar d-flex align-items-center gap-2">
            <a href="{{ route('admin.fines.index') }}"
                class="btn-filter {{ !request('status') ? 'active' : '' }}">
                📋 Semua
            </a>
            <a href="{{ route('admin.fines.index', ['status' => 'unpaid']) }}"
                class="btn-filter {{ request('status') === 'unpaid' ? 'active' : '' }}">
                ⏰ Belum Dikembalikan
            </a>
            <a href="{{ route('admin.fines.index', ['status' => 'paid']) }}"
                class="btn-filter {{ request('status') === 'paid' ? 'active' : '' }}">
                ✅ Sudah Dikembalikan
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
                        <th style="min-width:80px;">Status</th>
                        <th style="min-width:110px;">Tanggal Lunas</th>
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
                            @if($fine->status === 'paid')
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="comic-empty">
                                <span class="empty-emoji">🎉</span>
                                <div class="empty-title">TIDAK ADA DATA KETERLAMBATAN</div>
                                <div class="empty-sub">Semua keterlambatan sudah dikembalikan atau belum ada keterlambatan</div>
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
