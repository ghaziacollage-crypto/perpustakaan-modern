@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan & Statistik')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Sistem</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Laporan</li>
</ul>
@endsection

@push('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

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
.rank-number {
    width: 30px; height: 30px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 2px solid var(--comic-dark);
    font-family: 'Bangers', cursive;
    font-size: 1rem;
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

{{-- Year Selector --}}
<form method="GET" class="comic-search-bar mb-5" style="max-width:400px;">
    <div class="row g-3 align-items-end">
        <div class="col-md-8">
            <label class="form-label">📅 TAHUN</label>
            <select name="year" class="form-select form-select-solid" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
</form>

{{-- Summary Cards --}}
<div class="row g-4 mb-5">
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-blue) !important;">
            <div class="ssc-label">📤 TOTAL PEMINJAMAN</div>
            <div class="ssc-value" style="color:var(--comic-blue);">{{ number_format($summary['total_borrowings']) }}</div>
            <div class="ssc-icon">📤</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-yellow) !important;">
            <div class="ssc-label">📋 AKTIF SAAT INI</div>
            <div class="ssc-value" style="color:#b07d00;">{{ number_format($summary['active_borrowings']) }}</div>
            <div class="ssc-icon">📋</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-red) !important;">
            <div class="ssc-label">⚠️ TERLAMBAT</div>
            <div class="ssc-value" style="color:var(--comic-red);">{{ number_format($summary['overdue_count']) }}</div>
            <div class="ssc-icon">⚠️</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-stat-card" style="border-top:5px solid var(--comic-green) !important;">
            <div class="ssc-label">💰 TOTAL KETERLAMBATAN</div>
            <div class="ssc-value" style="color:var(--comic-green); font-size:1.6rem;">Rp {{ number_format($fineSummary['paid'] + $fineSummary['unpaid'], 0, ',', '.') }}</div>
            <div class="ssc-icon">💰</div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-4 mb-4">
    {{-- Monthly Borrowing Chart --}}
    <div class="col-lg-8">
        <div class="card report-card h-100">
            <div class="card-header">
                <div class="card-title">📊 GRAFIK PEMINJAMAN {{ $year }}</div>
            </div>
            <div class="card-body p-4">
                <div style="position:relative; height:260px;">
                    <canvas id="borrowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Fine Summary --}}
    <div class="col-lg-4">
        <div class="card report-card h-100">
            <div class="card-header">
                <div class="card-title">💰 RINGKASAN KETERLAMBATAN</div>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    <div style="background:#fff8f0; border:2px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); padding:12px 14px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size:0.78rem; font-weight:900; color:#b07d00;">⏰ BELUM LUNAS</span>
                            <span style="font-family:'Bangers',cursive; font-size:1.2rem; color:#b07d00;">{{ $fineSummary['pending_count'] }} keterlambatan</span>
                        </div>
                        <div style="font-family:'Bangers',cursive; font-size:1.1rem; color:#b07d00; margin-top:4px;">
                            Rp {{ number_format($fineSummary['unpaid'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="background:#f0fff4; border:2px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); padding:12px 14px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size:0.78rem; font-weight:900; color:var(--comic-green);">✅ SUDAH LUNAS</span>
                            <span style="font-family:'Bangers',cursive; font-size:1.2rem; color:var(--comic-green);">{{ $fineSummary['paid_count'] }} keterlambatan</span>
                        </div>
                        <div style="font-family:'Bangers',cursive; font-size:1.1rem; color:var(--comic-green); margin-top:4px;">
                            Rp {{ number_format($fineSummary['paid'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="background:var(--comic-dark); border:2px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-orange); padding:12px 14px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size:0.78rem; font-weight:900; color:rgba(255,255,255,0.6);">💰 TOTAL</span>
                            <span style="font-family:'Bangers',cursive; font-size:1.2rem; color:var(--comic-orange);">Rp {{ number_format($fineSummary['paid'] + $fineSummary['unpaid'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.fines.index') }}" class="btn btn-comic mt-4 w-100" style="margin-top:16px !important;">
                    💰 KELOLA KETERLAMBATAN
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Rankings Row --}}
<div class="row g-4 mb-4">
    {{-- Most Borrowed Books --}}
    <div class="col-lg-6">
        <div class="card report-card">
            <div class="card-header">
                <div class="card-title">📕 BUKU PALING DIPINJAM</div>
            </div>
            <div class="card-body p-4">
                <div class="comic-table-wrap">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th style="min-width:40px;">#</th>
                                <th style="min-width:200px;">Judul</th>
                                <th class="text-center" style="min-width:70px;">Total</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold">
                            @forelse($popularBooks as $index => $item)
                            <tr>
                                <td>
                                    <span class="rank-number rank-{{ $index === 0 ? '1' : ($index === 1 ? '2' : ($index === 2 ? '3' : 'other')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold" style="font-size:0.85rem;">{{ $item->book->title ?? '-' }}</span>
                                    <div style="font-size:0.72rem; color:#aaa; font-weight:700;">{{ $item->book->book_code ?? '' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-primary" style="font-size:0.82rem; border-radius:0 !important; border:2px solid var(--comic-blue) !important; color:var(--comic-blue) !important;">
                                        {{ $item->total }}x
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-6" style="font-size:0.85rem; font-weight:700;">Belum ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Most Active Members --}}
    <div class="col-lg-6">
        <div class="card report-card">
            <div class="card-header">
                <div class="card-title">👥 ANGGOTA PALING AKTIF</div>
            </div>
            <div class="card-body p-4">
                <div class="comic-table-wrap">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th style="min-width:40px;">#</th>
                                <th style="min-width:200px;">Nama</th>
                                <th class="text-center" style="min-width:70px;">Total</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold">
                            @forelse($activeMembers as $index => $item)
                            <tr>
                                <td>
                                    <span class="rank-number rank-{{ $index === 0 ? '1' : ($index === 1 ? '2' : ($index === 2 ? '3' : 'other')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="symbol symbol-30px flex-shrink-0">
                                            <div class="symbol-label fs-5 fw-bold"
                                                style="background:var(--comic-cream); color:var(--comic-dark); border:2px solid var(--comic-dark); font-size:0.7rem;">
                                                {{ strtoupper(substr($item->member->name ?? '?', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-gray-800 fw-bold" style="font-size:0.85rem;">{{ $item->member->name ?? '-' }}</span>
                                            <div style="font-size:0.72rem; color:#aaa; font-weight:700;">{{ $item->member->member_code ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-success" style="font-size:0.82rem; border-radius:0 !important; border:2px solid var(--comic-green) !important; color:var(--comic-green) !important;">
                                        {{ $item->total }}x
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-6" style="font-size:0.85rem; font-weight:700;">Belum ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Overdue List --}}
@if($overdueList->isNotEmpty())
<div class="card report-card">
    <div class="card-header">
        <div class="card-title">⚠️ DAFTAR PEMINJAMAN TERLAMBAT</div>
        <span class="badge badge-light-danger" style="font-size:0.85rem; border-radius:0 !important; border:2px solid var(--comic-red) !important; color:var(--comic-red) !important;">
            {{ $overdueList->count() }} buku
        </span>
    </div>
    <div class="card-body p-4">
        <div class="comic-table-wrap">
            <table class="table align-middle">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:150px;">Anggota</th>
                        <th style="min-width:180px;">Buku</th>
                        <th style="min-width:110px;">Jatuh Tempo</th>
                        <th style="min-width:80px;">Terlambat</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold">
                    @foreach($overdueList as $borrowing)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="symbol symbol-30px flex-shrink-0">
                                    <div class="symbol-label fs-5 fw-bold"
                                        style="background:var(--comic-cream); color:var(--comic-dark); border:2px solid var(--comic-dark); font-size:0.7rem;">
                                        {{ strtoupper(substr($borrowing->member->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-800 fw-bold" style="font-size:0.85rem;">{{ $borrowing->member->name }}</span>
                                    <div style="font-size:0.72rem; color:#aaa; font-weight:700;">{{ $borrowing->member->member_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @foreach($borrowing->details as $detail)
                                <div style="font-size:0.82rem; color:#555; font-weight:700;">📕 {{ Str::limit($detail->book->title, 30) }}</div>
                            @endforeach
                        </td>
                        <td>
                            <span style="font-family:'Fredoka One', cursive; font-size:0.82rem; color:var(--comic-red); font-weight:900;">
                                {{ $borrowing->due_date->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-light-danger" style="font-size:0.82rem; border-radius:0 !important; border:2px solid var(--comic-red) !important; color:var(--comic-red) !important;">
                                {{ $borrowing->daysOverdue() }} hari
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var comicFont = { family: "'Nunito', sans-serif", weight: '800' };
    var gridColor = '#eee';

    var chartCtx = document.getElementById('borrowChart') ? document.getElementById('borrowChart').getContext('2d') : null;
    if (chartCtx) {
        new Chart(chartCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: {!! json_encode($chartData['data']) !!},
                    backgroundColor: '#FF6B35',
                    borderColor: '#1A1A2E',
                    borderWidth: 2,
                    borderRadius: 0,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1A1A2E',
                        titleFont: { family: "'Nunito', sans-serif", weight: '800' },
                        bodyFont: { family: "'Nunito', sans-serif", weight: '700' },
                        padding: 10,
                        cornerRadius: 0,
                        callbacks: { label: function(ctx) { return ctx.parsed.y + ' peminjaman'; } }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Nunito', sans-serif", weight: '800', size: 10 }, color: '#888' },
                        border: { color: gridColor }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { font: { family: "'Nunito', sans-serif", weight: '800', size: 10 }, color: '#888', stepSize: 1 },
                        border: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush