@extends('layouts.app')

@section('title', 'Laporan Kriteria Buku')
@section('page-title', 'Laporan Kriteria Buku')

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
    <li class="breadcrumb-item text-gray-900">Kriteria Buku</li>
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
.summary-stat-card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 4px 4px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
    padding: 16px 18px;
    background: #fff;
}
.rank-number {
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 2px solid var(--comic-dark);
    font-family: 'Bangers', cursive;
    font-size: 0.95rem;
    color: #fff;
    flex-shrink: 0;
}
.rank-1 { background: var(--comic-orange); }
.rank-2 { background: var(--comic-yellow); color: var(--comic-dark); }
.rank-3 { background: var(--comic-blue); }
.rank-other { background: #eee; color: #888; }
</style>
@endpush

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-blue) !important;">
            <div style="font-size:0.68rem; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:1px;">TOTAL BUKU</div>
            <div style="font-family:'Bangers',cursive; font-size:2rem; line-height:1; color:var(--comic-blue);">{{ number_format($totalBooks) }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-green) !important;">
            <div style="font-size:0.68rem; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:1px;">NORMAL</div>
            <div style="font-family:'Bangers',cursive; font-size:2rem; line-height:1; color:var(--comic-green);">{{ number_format($totalNormal) }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-yellow) !important;">
            <div style="font-size:0.68rem; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:1px;">RUSAK</div>
            <div style="font-family:'Bangers',cursive; font-size:2rem; line-height:1; color:#b07d00;">{{ number_format($totalRusak) }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-red) !important;">
            <div style="font-size:0.68rem; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:1px;">HILANG</div>
            <div style="font-family:'Bangers',cursive; font-size:2rem; line-height:1; color:var(--comic-red);">{{ number_format($totalHilang) }}</div>
        </div>
    </div>
</div>

{{-- Filter Form --}}
<div class="report-form-wrap">
    <form method="GET" action="{{ route('admin.reports.books.index') }}">
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
                <label>Kategori</label>
                <select name="category" class="form-select form-select-solid" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
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
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-comic w-100" style="background:var(--comic-blue) !important; color:#fff !important; border-color:var(--comic-dark) !important; box-shadow:3px 3px 0 var(--comic-dark) !important; border-radius:0 !important;">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Top 10 Buku Paling Sering Dipinjam --}}
<div class="card mb-4">
    <div class="card-header border-0 pt-6" style="background: var(--comic-dark) !important;">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📕 BUku PALING SERING DIPINJAM</span>
        </div>
    </div>
    <div class="card-body py-4 px-4">
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:40px;">#</th>
                        <th style="min-width:200px;">Judul</th>
                        <th style="min-width:100px;">Kategori</th>
                        <th style="min-width:60px; text-align:center;">Dipinjam</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold">
                    @forelse($topBooks as $index => $book)
                    <tr>
                        <td>
                            <span class="rank-number rank-{{ $index === 0 ? '1' : ($index === 1 ? '2' : ($index === 2 ? '3' : 'other')) }}">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:0.85rem; font-weight:900;">{{ $book->title ?? '-' }}</span>
                            <div style="font-size:0.7rem; color:#aaa; font-weight:700;">{{ $book->book_code ?? '' }}</div>
                        </td>
                        <td>{{ $book->category?->name ?? '-' }}</td>
                        <td style="text-align:center;">
                            <span style="font-family:'Bangers',cursive; font-size:1.1rem; color:var(--comic-blue);">{{ $book->borrow_count }}x</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div style="text-align:center; padding:30px;">
                                <span class="empty-emoji">📚</span>
                                <div style="font-weight:700; color:#aaa;">Belum ada data peminjaman</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Main Table --}}
<div class="card">
    <div class="card-header border-0 pt-6" style="background: var(--comic-dark) !important;">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📖 DAFTAR BUKU</span>
            <span class="badge badge-light-primary ms-2" style="font-size:0.72rem; border-radius:0 !important;">{{ $books->total() }} data</span>
        </div>
    </div>
    <div class="card-body py-4 px-4">
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:40px;">#</th>
                        <th style="min-width:180px;">Kode / Judul</th>
                        <th style="min-width:100px;">Kategori</th>
                        <th style="min-width:120px;">Penulis</th>
                        <th style="min-width:50px; text-align:center;">Stok</th>
                        <th style="min-width:70px; text-align:center;">Kondisi</th>
                        <th style="min-width:80px; text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($books as $index => $book)
                    <tr>
                        <td><span style="font-family:'Bangers',cursive; font-size:1rem; color:var(--comic-orange);">{{ $books->firstItem() + $index }}</span></td>
                        <td>
                            <div style="font-size:0.85rem; font-weight:900;">{{ $book->title ?? '-' }}</div>
                            <span style="font-size:0.72rem; color:#aaa;">{{ $book->book_code ?? '' }}</span>
                        </td>
                        <td>{{ $book->category?->name ?? '-' }}</td>
                        <td>{{ $book->author ?? '-' }}</td>
                        <td style="text-align:center;">{{ $book->stock ?? 0 }}</td>
                        <td style="text-align:center;">
                            @switch($book->kondisi->value ?? 'normal')
                                @case('normal')
                                    <span class="badge badge-light-success" style="font-size:0.72rem; border-radius:0 !important;">✅ Normal</span>
                                    @break
                                @case('rusak')
                                    <span class="badge badge-light-warning" style="font-size:0.72rem; border-radius:0 !important;">⚠️ Rusak</span>
                                    @break
                                @case('hilang')
                                    <span class="badge badge-light-danger" style="font-size:0.72rem; border-radius:0 !important;">❌ Hilang</span>
                                    @break
                                @default
                                    <span class="badge badge-light" style="font-size:0.72rem; border-radius:0 !important;">-</span>
                            @endswitch
                        </td>
                        <td style="text-align:center;">
                            @if(($book->status->value ?? '') === 'available' && ($book->stock ?? 0) > 0)
                                <span class="badge badge-light-success" style="font-size:0.72rem; border-radius:0 !important;">🟢 Tersedia</span>
                            @else
                                <span class="badge badge-light-danger" style="font-size:0.72rem; border-radius:0 !important;">🔴 Tidak</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div style="text-align:center; padding:40px;">
                                <span class="empty-emoji">📚</span>
                                <div style="font-weight:700; color:#aaa;">TIDAK ADA DATA BUKU</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('layouts.partials._pagination', ['paginator' => $books])
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 mt-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Laporan</a>
    <a href="{{ route('admin.reports.books.pdf', request()->all()) }}" target="_blank" class="btn btn-comic" style="background:var(--comic-red) !important; color:#fff !important; border-color:var(--comic-dark) !important; box-shadow:3px 3px 0 var(--comic-dark) !important; border-radius:0 !important;">
        <i class="ki-duotone ki-file-down fs-5"></i> Download PDF
    </a>
</div>

@endsection
