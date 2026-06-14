@extends('layouts.app')

@section('title', 'Laporan Data Anggota')
@section('page-title', 'Laporan Data Anggota')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Sistem</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Laporan Anggota</li>
</ul>
@endsection

@push('custom-css')
<style>
.summary-stat-card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 5px 5px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
    padding: 20px 22px;
    background: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.2s;
}
.summary-stat-card:hover { transform: translateY(-3px); box-shadow: 7px 9px 0 var(--comic-dark) !important; }
.summary-stat-card .ssc-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.68rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #aaa;
}
.summary-stat-card .ssc-value {
    font-family: 'Bangers', cursive;
    font-size: 2.2rem;
    line-height: 1;
    text-shadow: 2px 2px 0 rgba(0,0,0,0.1);
}
.summary-stat-card .ssc-icon { font-size: 2.5rem; opacity: 0.2; position: absolute; right: 12px; bottom: 8px; }
.report-card { border: 3px solid var(--comic-dark) !important; box-shadow: 4px 4px 0 var(--comic-dark) !important; border-radius: 0 !important; }
.report-card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
    padding: 14px 20px;
}
.report-card .card-header .card-title {
    font-family: 'Bangers', cursive !important;
    letter-spacing: 2px !important;
    color: var(--comic-orange) !important;
    font-size: 1.1rem !important;
}
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="comic-search-bar mb-5">
    <form method="GET" action="{{ route('admin.reports.members.index') }}">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">🔍 Pencarian</label>
                <input type="text" name="search" class="form-control form-control-solid" value="{{ request('search') }}" placeholder="Nama, kode, NIS/NIM...">
            </div>
            <div class="col-md-2">
                <label class="form-label">📚 Kelas</label>
                <select name="class_filter" class="form-select form-select-solid">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}" {{ $classFilter == $class ? 'selected' : '' }}>{{ $class }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">📊 Status</label>
                <select name="status_filter" class="form-select form-select-solid">
                    <option value="">Semua Status</option>
                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-comic" style="background:var(--comic-dark) !important; color:#fff !important;">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.reports.members.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                <a href="{{ route('admin.reports.members.pdf', request()->all()) }}" target="_blank" class="btn btn-comic" style="background:var(--comic-red) !important; color:#fff !important; border-color:var(--comic-dark) !important; box-shadow:3px 3px 0 var(--comic-dark) !important; border-radius:0 !important;">
                    📄 Export PDF
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-5">
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-blue) !important;">
            <div class="ssc-label">👥 TOTAL ANGGOTA</div>
            <div class="ssc-value" style="color:var(--comic-blue);">{{ number_format($totalMembers) }}</div>
            <div class="ssc-icon">👥</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-green) !important;">
            <div class="ssc-label">✅ AKTIF</div>
            <div class="ssc-value" style="color:var(--comic-green);">{{ number_format($totalActive) }}</div>
            <div class="ssc-icon">✅</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-red) !important;">
            <div class="ssc-label">⛌ TIDAK AKTIF</div>
            <div class="ssc-value" style="color:var(--comic-red);">{{ number_format($totalInactive) }}</div>
            <div class="ssc-icon">⛌</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid #b07d00 !important;">
            <div class="ssc-label">⏳ PENDING</div>
            <div class="ssc-value" style="color:#b07d00;">{{ number_format($totalPending) }}</div>
            <div class="ssc-icon">⏳</div>
        </div>
    </div>
</div>

{{-- Members Table --}}
<div class="card report-card">
    <div class="card-header">
        <div class="card-title">📋 DAFTAR ANGGOTA</div>
        <span class="badge badge-light-primary" style="font-size:0.85rem; border-radius:0 !important; border:2px solid var(--comic-blue) !important; color:var(--comic-blue) !important;">
            {{ $members->total() }} anggota
        </span>
    </div>
    <div class="card-body p-4">
        <div class="comic-table-wrap">
            <table class="table align-middle">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:40px;">#</th>
                        <th style="min-width:160px;">Anggota</th>
                        <th style="min-width:100px;">Kode</th>
                        <th style="min-width:80px;">Kelas</th>
                        <th style="min-width:80px;">Status</th>
                        <th class="text-center" style="min-width:80px;">Pinjam</th>
                        <th class="text-center" style="min-width:100px;">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold">
                    @forelse($members as $index => $member)
                    <tr>
                        <td>{{ $members->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="symbol symbol-30px flex-shrink-0">
                                    <div class="symbol-label fs-5 fw-bold"
                                        style="background:var(--comic-cream); color:var(--comic-dark); border:2px solid var(--comic-dark); font-size:0.7rem;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-800 fw-bold" style="font-size:0.85rem;">{{ $member->name }}</span>
                                    <div style="font-size:0.72rem; color:#aaa;">{{ $member->nis_nim ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-family:'Fredoka One', cursive; font-size:0.82rem;">{{ $member->member_code }}</span>
                        </td>
                        <td>
                            <span class="badge badge-light-warning" style="font-size:0.82rem; border-radius:0 !important; border:2px solid var(--comic-orange) !important; color:#b07d00 !important;">
                                {{ $member->class ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @if($member->status === 'active')
                                <span class="badge badge-light-success" style="font-size:0.78rem; border-radius:0 !important; border:2px solid var(--comic-green) !important; color:var(--comic-green) !important;">Aktif</span>
                            @elseif($member->status === 'inactive')
                                <span class="badge badge-light-danger" style="font-size:0.78rem; border-radius:0 !important; border:2px solid var(--comic-red) !important; color:var(--comic-red) !important;">Tidak Aktif</span>
                            @else
                                <span class="badge badge-light-warning" style="font-size:0.78rem; border-radius:0 !important; border:2px solid #b07d00 !important; color:#b07d00 !important;">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-primary" style="font-size:0.82rem; border-radius:0 !important; border:2px solid var(--comic-blue) !important; color:var(--comic-blue) !important;">
                                {{ $member->total_borrowings ?? 0 }}x
                            </span>
                        </td>
                        <td>
                            <span style="font-size:0.78rem; color:#888;">{{ $member->created_at->format('d M Y') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-6">Belum ada data anggota</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $members->links() }}
        </div>
    </div>
</div>

@endsection
