@extends('layouts.app')

@section('title', 'Laporan Peminjaman & Pengembalian')
@section('page-title', 'Laporan Peminjaman & Pengembalian')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Sistem</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Laporan</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Peminjaman</li>
</ul>
@endsection

@push('custom-css')
<style>
.report-form-wrap {
    background: #fff;
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 5px 5px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
    padding: 20px 24px;
    margin-bottom: 24px;
}
.report-form-wrap label { font-size: 0.72rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Laporan</a>
    <a href="{{ route('admin.reports.borrowings.pdf', request()->all()) }}" target="_blank" class="btn btn-comic" style="background:var(--comic-red) !important; color:#fff !important; border-color:var(--comic-dark) !important; box-shadow:3px 3px 0 var(--comic-dark) !important; border-radius:0 !important;">
        <i class="ki-duotone ki-file-down fs-5"></i> Download PDF
    </a>
</div>

<div class="report-form-wrap">
    <form method="GET" action="{{ route('admin.reports.borrowings.index') }}">
        <div class="row g-3">
            <div class="col-md-2">
                <label>Periode</label>
                <select name="period" class="form-select form-select-solid" onchange="this.form.submit()">
                    <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control form-select-solid" value="{{ $startDate }}">
            </div>
            <div class="col-md-2">
                <label>Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control form-select-solid" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
                <label>Tipe</label>
                <select name="type" class="form-select form-select-solid" onchange="this.form.submit()">
                    <option value="both" {{ $type === 'both' ? 'selected' : '' }}>Peminjaman & Pengembalian</option>
                    <option value="borrowing" {{ $type === 'borrowing' ? 'selected' : '' }}>Peminjaman Saja</option>
                    <option value="return" {{ $type === 'return' ? 'selected' : '' }}>Pengembalian Saja</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Kelas</label>
                <select name="class_filter" class="form-select form-select-solid" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @php
                        $classes = \App\Models\Member::distinct()->pluck('class')->filter()->sort()->values();
                    @endphp
                    @foreach($classes as $cls)
                        <option value="{{ $cls }}" {{ request('class_filter') === $cls ? 'selected' : '' }}>{{ $cls }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Cari</label>
                <input type="text" name="search" class="form-control form-select-solid" placeholder="Nama anggota..." value="{{ request('search') }}">
            </div>
        </div>
    </form>
</div>

{{-- Peminjaman Section --}}
@if($type !== 'return')
<div class="card mb-4">
    <div class="card-header border-0 pt-6" style="background: var(--comic-dark) !important;">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📤 PEMINJAMAN</span>
            <span class="badge badge-light-primary ms-2" style="font-size:0.72rem; border-radius:0 !important;">{{ $borrowings->total() }} data</span>
        </div>
    </div>
    <div class="card-body py-4 px-4">
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:40px;">#</th>
                        <th style="min-width:100px;">Tanggal Pinjam</th>
                        <th style="min-width:140px;">Anggota</th>
                        <th style="min-width:80px;">Kelas</th>
                        <th style="min-width:200px;">Buku</th>
                        <th style="min-width:90px;">Jatuh Tempo</th>
                        <th style="min-width:80px;">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($borrowings as $index => $b)
                    <tr>
                        <td><span class="fw-bold" style="color:var(--comic-orange); font-family:'Bangers',cursive; font-size:1rem;">{{ $borrowings->firstItem() + $index }}</span></td>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($b->loan_date)->isoFormat('D MMM Y') }}</td>
                        <td>
                            <div class="fw-bold text-dark d-block" style="font-size:0.85rem;">{{ $b->member->name ?? '-' }}</div>
                            <span class="text-muted" style="font-size:0.72rem;">{{ $b->member->member_code ?? '' }}</span>
                        </td>
                        <td>{{ $b->member->class ?? '-' }}</td>
                        <td>
                            @foreach($b->details as $detail)
                                <span style="font-size:0.8rem; color:#555;">{{ Str::limit($detail->book->title ?? '-', 25) }}</span><br>
                            @endforeach
                        </td>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($b->due_date)->isoFormat('D MMM Y') }}</td>
                        <td>
                            @if($b->status->value === 'returned')
                                <span class="badge badge-light-success" style="font-size:0.72rem;">✅ Selesai</span>
                            @elseif($b->isOverdue())
                                <span class="badge badge-light-danger" style="font-size:0.72rem;">⚠️ Terlambat {{ $b->daysOverdue() }} hari</span>
                            @else
                                <span class="badge badge-light-primary" style="font-size:0.72rem;">📋 Aktif</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="comic-empty" style="text-align:center; padding:30px;">
                                <span class="empty-emoji">📋</span>
                                <div class="empty-title">TIDAK ADA DATA PEMINJAMAN</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('layouts.partials._pagination', ['paginator' => $borrowings])
    </div>
</div>
@endif

{{-- Pengembalian Section --}}
@if($type !== 'borrowing' && $type !== 'both')
<div class="card mb-4">
    <div class="card-header border-0 pt-6" style="background: var(--comic-green) !important;">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📥 PENGEMBALIAN</span>
            <span class="badge badge-light-success ms-2" style="font-size:0.72rem; border-radius:0 !important;">{{ $returns->total() }} data</span>
        </div>
    </div>
    <div class="card-body py-4 px-4">
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:40px;">#</th>
                        <th style="min-width:100px;">Tanggal Kembali</th>
                        <th style="min-width:140px;">Anggota</th>
                        <th style="min-width:80px;">Kelas</th>
                        <th style="min-width:200px;">Buku</th>
                        <th style="min-width:90px;">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($returns as $index => $b)
                    <tr>
                        <td><span class="fw-bold" style="color:var(--comic-orange); font-family:'Bangers',cursive; font-size:1rem;">{{ $returns->firstItem() + $index }}</span></td>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($b->return_date)->isoFormat('D MMM Y') }}</td>
                        <td>
                            <div class="fw-bold text-dark d-block" style="font-size:0.85rem;">{{ $b->member->name ?? '-' }}</div>
                            <span class="text-muted" style="font-size:0.72rem;">{{ $b->member->member_code ?? '' }}</span>
                        </td>
                        <td>{{ $b->member->class ?? '-' }}</td>
                        <td>
                            @foreach($b->details as $detail)
                                <span style="font-size:0.8rem; color:#555;">{{ Str::limit($detail->book->title ?? '-', 25) }}</span><br>
                            @endforeach
                        </td>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($b->due_date)->isoFormat('D MMM Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="comic-empty" style="text-align:center; padding:30px;">
                                <span class="empty-emoji">📦</span>
                                <div class="empty-title">TIDAK ADA DATA PENGEMBALIAN</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('layouts.partials._pagination', ['paginator' => $returns])
    </div>
</div>
@endif
@if($type === 'both')
<div class="card mb-4">
    <div class="card-header border-0 pt-6" style="background: var(--comic-green) !important;">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📥 PENGEMBALIAN</span>
            <span class="badge badge-light-success ms-2" style="font-size:0.72rem; border-radius:0 !important;">{{ $returns->total() }} data</span>
        </div>
    </div>
    <div class="card-body py-4 px-4">
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:40px;">#</th>
                        <th style="min-width:100px;">Tanggal Kembali</th>
                        <th style="min-width:140px;">Anggota</th>
                        <th style="min-width:80px;">Kelas</th>
                        <th style="min-width:200px;">Buku</th>
                        <th style="min-width:90px;">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($returns as $index => $b)
                    <tr>
                        <td><span class="fw-bold" style="color:var(--comic-orange); font-family:'Bangers',cursive; font-size:1rem;">{{ $returns->firstItem() + $index }}</span></td>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($b->return_date)->isoFormat('D MMM Y') }}</td>
                        <td>
                            <div class="fw-bold text-dark d-block" style="font-size:0.85rem;">{{ $b->member->name ?? '-' }}</div>
                            <span class="text-muted" style="font-size:0.72rem;">{{ $b->member->member_code ?? '' }}</span>
                        </td>
                        <td>{{ $b->member->class ?? '-' }}</td>
                        <td>
                            @foreach($b->details as $detail)
                                <span style="font-size:0.8rem; color:#555;">{{ Str::limit($detail->book->title ?? '-', 25) }}</span><br>
                            @endforeach
                        </td>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($b->due_date)->isoFormat('D MMM Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="comic-empty" style="text-align:center; padding:30px;">
                                <span class="empty-emoji">📦</span>
                                <div class="empty-title">TIDAK ADA DATA PENGEMBALIAN</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('layouts.partials._pagination', ['paginator' => $returns])
    </div>
</div>
@endif

@endsection
