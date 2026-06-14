@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('custom-css')
<style>
/* ── Override page-header gap for dashboard (no breadcrumb) ── */
.page-header, #kt_toolbar { display: none !important; }
#kt_content { background: var(--comic-cream); padding-top: 0; }
#kt_content_container { padding-top: 20px; }

/* ── Welcome Header ── */
.dashboard-welcome {
    border: 3px solid var(--comic-dark);
    box-shadow: 5px 5px 0 var(--comic-dark);
    background: linear-gradient(135deg, var(--comic-dark) 0%, #2d2d4a 100%);
    border-radius: 0;
    padding: 24px 30px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.dashboard-welcome::before {
    content: '📚';
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 4.5rem;
    opacity: 0.12;
}
.dashboard-welcome h1 {
    font-family: 'Bangers', cursive;
    color: var(--comic-orange);
    font-size: 1.6rem;
    letter-spacing: 2px;
    margin-bottom: 4px;
    line-height: 1;
}
.dashboard-welcome .welcome-sub {
    font-family: 'Fredoka One', cursive;
    color: rgba(255,255,255,0.55);
    font-size: 0.75rem;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.dashboard-welcome .welcome-date {
    font-family: 'Fredoka One', cursive;
    color: rgba(255,255,255,0.38);
    font-size: 0.7rem;
    letter-spacing: 1px;
    margin-top: 4px;
}

/* ── Stat Cards ── */
.comic-stat-card {
    display: flex; align-items: center; gap: 14px; padding: 18px 16px;
    border: 3px solid var(--comic-dark); box-shadow: 5px 5px 0 var(--comic-dark);
    background: #fff; text-decoration: none; transition: all 0.2s ease;
    position: relative; overflow: hidden; border-radius: 0;
}
.comic-stat-card:hover { transform: translateY(-4px); box-shadow: 7px 9px 0 var(--comic-dark); }
.csc-books { border-top: 5px solid var(--comic-blue); }
.csc-books:hover { background: rgba(78,205,196,0.08); }
.csc-books .csc-number { color: var(--comic-blue); }
.csc-members { border-top: 5px solid var(--comic-orange); }
.csc-members:hover { background: rgba(255,107,53,0.08); }
.csc-members .csc-number { color: var(--comic-orange); }
.csc-borrowed { border-top: 5px solid #3498db; }
.csc-borrowed:hover { background: rgba(52,152,219,0.08); }
.csc-borrowed .csc-number { color: #3498db; }
.csc-overdue { border-top: 5px solid var(--comic-red); }
.csc-overdue:hover { background: rgba(255,51,102,0.08); }
.csc-overdue .csc-number { color: var(--comic-red); }
.csc-icon { font-size: 2.2rem; flex-shrink: 0; }
.csc-body { flex: 1; }
.csc-number { font-family: 'Bangers', cursive; font-size: 2.2rem; line-height: 1; }
.csc-label { font-family: 'Fredoka One', cursive; font-size: 0.65rem; color: #bbb; letter-spacing: 2px; text-transform: uppercase; margin-top: 4px; }
.csc-corner { font-size: 2.8rem; opacity: 0.12; position: absolute; right: 8px; bottom: 4px; }

/* ── Chart Card (comic-styled) ── */
.dash-chart-card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 5px 5px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
    overflow: hidden !important;
}
.dash-chart-card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
    border-radius: 0 !important;
    padding: 12px 18px !important;
}
.dash-chart-card .card-header .card-title {
    font-family: 'Bangers', cursive !important;
    color: var(--comic-orange) !important;
    font-size: 0.95rem !important;
    letter-spacing: 2px !important;
}
.dash-chart-card .card-body { padding: 16px !important; }

/* ── Doughnut wrapper — keep chart inside card ── */
.donut-wrapper {
    position: relative;
    width: 140px;
    height: 140px;
    margin: 0 auto;
}
.donut-wrapper canvas { display: block; }
.donut-center {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; pointer-events: none;
}
.donut-center .donut-num {
    font-family: 'Bangers', cursive;
    font-size: 1.9rem;
    color: var(--comic-dark);
    line-height: 1;
}
.donut-center .donut-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.55rem;
    color: #bbb;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* ── Legend row ── */
.donut-legend { display: flex; flex-direction: column; gap: 8px; }
.donut-legend-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 5px 8px;
    border: 2px solid var(--comic-dark);
    background: #fff;
}
.donut-legend-row:last-child { border-bottom: none; }
.donut-legend-row .lbl {
    display: flex; align-items: center; gap: 8px;
    font-family: 'Fredoka One', cursive;
    font-size: 0.8rem;
    color: var(--comic-dark);
}
.donut-legend-row .lbl .dot {
    width: 10px; height: 10px;
    border: 2px solid var(--comic-dark);
    flex-shrink: 0;
}
.donut-legend-row .val {
    font-family: 'Bangers', cursive;
    font-size: 1.1rem;
}

/* ── Quick Actions ── */
.quick-action-btn {
    display: flex; align-items: center; gap: 12px; padding: 12px 14px;
    border: 2px solid var(--comic-dark); box-shadow: 3px 3px 0 var(--comic-dark);
    background: #fff; text-decoration: none; transition: all 0.18s ease;
    border-radius: 0;
}
.quick-action-btn:hover { transform: translateX(4px); box-shadow: 5px 5px 0 var(--comic-dark); }
.qab-icon { font-size: 1.6rem; flex-shrink: 0; }
.qab-text { flex: 1; display: flex; flex-direction: column; }
.qab-text strong { font-family: 'Fredoka One', cursive; font-size: 0.85rem; color: var(--comic-dark); }
.qab-text small { font-size: 0.7rem; color: #bbb; font-weight: 700; }
.qab-arrow { font-family: 'Bangers', cursive; font-size: 1.2rem; color: #ddd; transition: all 0.18s; }
.quick-action-btn:hover .qab-arrow { color: var(--comic-orange); transform: translateX(4px); }
.qab-primary { border-top: 4px solid var(--comic-orange); }
.qab-accent { border-top: 4px solid var(--comic-yellow); }
.qab-secondary { border-top: 4px solid #e8e8e8; }
.qab-danger { border-top: 4px solid var(--comic-red); }

/* ── Dashboard Table (full inline — no dependency on layout .table) ── */
.dash-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.dash-table thead tr {
    background: rgba(26, 26, 46, 0.05);
    border-bottom: 2px solid var(--comic-dark);
}
.dash-table thead tr th {
    font-family: 'Fredoka One', cursive;
    font-size: 0.65rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #888;
    padding: 10px 14px;
    white-space: nowrap;
    text-align: left;
    font-weight: 700;
}
.dash-table tbody tr {
    border-bottom: 1px solid rgba(26,26,46,0.07);
    transition: background 0.15s;
}
.dash-table tbody tr:hover { background: rgba(255,107,53,0.05); }
.dash-table tbody tr:last-child { border-bottom: none; }
.dash-table tbody tr td {
    padding: 10px 14px;
    vertical-align: middle;
    color: var(--comic-dark);
}
.dash-table .empty-state {
    text-align: center;
    padding: 24px;
    color: #aaa;
    font-family: 'Fredoka One', cursive;
    font-size: 0.82rem;
    letter-spacing: 1px;
}

/* ── Badge ── */
.badge-comic {
    display: inline-block;
    font-family: 'Fredoka One', cursive;
    font-size: 0.62rem;
    letter-spacing: 1px;
    padding: 3px 8px;
    border-radius: 0;
    border: 2px solid var(--comic-dark);
    line-height: 1.2;
    white-space: nowrap;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .csc-number { font-size: 1.7rem; }
    .csc-corner { display: none; }
    .dashboard-welcome h1 { font-size: 1.2rem; }
    .dashboard-welcome::before { display: none; }
}
</style>
@endpush

@section('content')
{{-- Welcome Header ─────────────────────────────────────────────── --}}
<div class="dashboard-welcome">
    <h1>⚡ SELAMAT DATANG DI PERPUSTAKAAN!</h1>
    <div class="welcome-sub">✨ Kelola perpustakaan digital anda dengan mudah & seru!</div>
    <div class="welcome-date">📅 {{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
</div>

{{-- Stat Cards ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.books.index') }}" class="comic-stat-card csc-books">
            <div class="csc-icon">📕</div>
            <div class="csc-body">
                <div class="csc-number">{{ number_format($totalBooks) }}</div>
                <div class="csc-label">TOTAL BUKU</div>
            </div>
            <div class="csc-corner">📖</div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.members.index') }}" class="comic-stat-card csc-members">
            <div class="csc-icon">👥</div>
            <div class="csc-body">
                <div class="csc-number">{{ number_format($totalMembers) }}</div>
                <div class="csc-label">TOTAL ANGGOTA</div>
            </div>
            <div class="csc-corner">👤</div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.returns.index') }}" class="comic-stat-card csc-borrowed">
            <div class="csc-icon">📤</div>
            <div class="csc-body">
                <div class="csc-number">{{ number_format($borrowedBooks) }}</div>
                <div class="csc-label">BUKU DIPINJAM</div>
            </div>
            <div class="csc-corner">🔄</div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.returns.index') }}" class="comic-stat-card csc-overdue">
            <div class="csc-icon">⏰</div>
            <div class="csc-body">
                <div class="csc-number">{{ number_format($overdueBooks) }}</div>
                <div class="csc-label">BUKU TERLAMBAT</div>
            </div>
            <div class="csc-corner">⚠️</div>
        </a>
    </div>
</div>

{{-- Row 1: Charts ──────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    {{-- Bar chart: Peminjaman Per Bulan --}}
    <div class="col-lg-8">
        <div class="card dash-chart-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title">📊 PEMINJAMAN PER BULAN</div>
                <span style="font-family:'Fredoka One',cursive; font-size:0.62rem; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase;">12 Bulan Terakhir</span>
            </div>
            <div class="card-body">
                <div style="position:relative; height:200px;">
                    <canvas id="chartMonthly"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Doughnut: Status Peminjaman — chart left, legend right --}}
    <div class="col-lg-4">
        <div class="card dash-chart-card h-100">
            <div class="card-header">
                <div class="card-title">📈 STATUS PEMINJAMAN</div>
            </div>
            <div class="card-body d-flex align-items-center">
                <div style="display:flex; align-items:center; gap:16px; width:100%;">
                    {{-- Chart left --}}
                    <div class="donut-wrapper" style="flex-shrink:0;">
                        <canvas id="chartStatus" width="130" height="130"></canvas>
                        <div class="donut-center">
                            <span class="donut-num">{{ array_sum($borrowingStatusCounts) }}</span>
                            <span class="donut-label">Total</span>
                        </div>
                    </div>
                    {{-- Legend right --}}
                    <div class="donut-legend" style="flex:1;">
                        <div class="donut-legend-row">
                            <span class="lbl"><span class="dot" style="background:var(--comic-yellow);"></span>Pending</span>
                            <span class="val" style="color:var(--comic-yellow);">{{ $borrowingStatusCounts['pending'] }}</span>
                        </div>
                        <div class="donut-legend-row">
                            <span class="lbl"><span class="dot" style="background:#4ECDC4;"></span>Aktif</span>
                            <span class="val" style="color:#4ECDC4;">{{ $borrowingStatusCounts['active'] }}</span>
                        </div>
                        <div class="donut-legend-row">
                            <span class="lbl"><span class="dot" style="background:#FF3366;"></span>Terlambat</span>
                            <span class="val" style="color:#FF3366;">{{ $borrowingStatusCounts['late'] }}</span>
                        </div>
                        <div class="donut-legend-row">
                            <span class="lbl"><span class="dot" style="background:#2ecc71;"></span>Kembali</span>
                            <span class="val" style="color:#2ecc71;">{{ $borrowingStatusCounts['returned'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Row 2: Kategori + Transaksi ─────────────────────────────────── --}}
<div class="row g-3 mb-3">
    {{-- Horizontal Bar: Buku per Kategori --}}
    <div class="col-lg-4">
        <div class="card dash-chart-card h-100">
            <div class="card-header">
                <div class="card-title">📚 BUKU PER KATEGORI</div>
            </div>
            <div class="card-body">
                @if($categoryBooks->count())
                <div style="position:relative; height:{{ max(160, $categoryBooks->count() * 38) }}px;">
                    <canvas id="chartCategory"></canvas>
                </div>
                @else
                <div class="empty-state">📂 BELUM ADA KATEGORI</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="col-lg-8">
        <div class="card dash-chart-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title">📋 TRANSAKSI TERBARU</div>
                <a href="{{ route('admin.returns.index') }}"
                    style="background:var(--comic-orange); color:#fff; font-family:'Fredoka One',cursive;
                           font-size:0.68rem; border-radius:0; border:2px solid var(--comic-dark);
                           box-shadow:2px 2px 0 var(--comic-dark); padding:4px 10px; text-decoration:none;">
                    Lihat Semua →
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentBorrowings->count())
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>👤 ANGGOTA</th>
                                <th>🔢 KODE</th>
                                <th>📅 PINJAM</th>
                                <th>⏰ JATUH TEMPO</th>
                                <th>⚡ STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBorrowings as $borrowing)
                            @php
                                $sm = [
                                    'pending'  => ['bg' => 'var(--comic-yellow)', 't' => 'PENDING'],
                                    'active'   => ['bg' => '#3498db', 't' => 'AKTIF'],
                                    'late'     => ['bg' => 'var(--comic-red)', 't' => 'TERLAMBAT'],
                                    'returned' => ['bg' => 'var(--comic-green)', 't' => 'KEMBALI'],
                                ];
                                $s = $sm[$borrowing->status->value] ?? ['bg' => '#aaa', 't' => strtoupper($borrowing->status->value)];
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; background:var(--comic-cream); border:2px solid var(--comic-dark);
                                                    display:flex; align-items:center; justify-content:center;
                                                    font-family:'Fredoka One',cursive; font-size:0.72rem; color:var(--comic-dark); flex-shrink:0;">
                                            {{ strtoupper(substr($borrowing->member->name, 0, 1)) }}
                                        </div>
                                        <span style="font-family:'Fredoka One',cursive; font-size:0.8rem; color:var(--comic-dark); white-space:nowrap;">
                                            {{ $borrowing->member->name }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-family:'Fredoka One',cursive; font-size:0.75rem; color:#bbb;">{{ $borrowing->transaction_code }}</span>
                                </td>
                                <td>
                                    <span style="font-size:0.78rem; color:#888;">{{ $borrowing->loan_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span style="font-family:'Fredoka One',cursive; font-size:0.78rem;
                                        {{ $borrowing->status->value === 'late' ? 'color:var(--comic-red);' : 'color:#888;' }}">
                                        {{ $borrowing->due_date->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-comic" style="background:{{ $s['bg'] }}; color:#fff;">
                                        {{ $s['t'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">📭 BELUM ADA TRANSAKSI</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Row 3: Pending Requests + Member Queue ─────────────────────────────── --}}
<div class="row g-3 mb-3">
    {{-- Pending Borrowing Requests --}}
    <div class="col-lg-8">
        <div class="card dash-chart-card">
            <div class="card-header d-flex align-items-center justify-content-between" style="background: var(--comic-yellow);">
                <div class="card-title" style="color: var(--comic-dark) !important;">
                    ⏳ REQUEST PEMINJAMAN MENUNGGU VERIFIKASI
                </div>
                <span style="font-family:'Fredoka One',cursive; font-size:0.68rem; color:var(--comic-dark); letter-spacing:1px;">
                    {{ $pendingBorrowings->count() }} request
                </span>
            </div>
            <div class="card-body p-0">
                @if($pendingBorrowings->count())
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>👤 ANGGOTA</th>
                                <th>📖 BUKU</th>
                                <th>📅 WAKTU</th>
                                <th>⚡ AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingBorrowings as $pending)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:32px; height:32px; background:var(--comic-cream); border:2px solid var(--comic-dark);
                                                    display:flex; align-items:center; justify-content:center;
                                                    font-family:'Fredoka One',cursive; font-size:0.75rem; color:var(--comic-dark); flex-shrink:0;">
                                            {{ strtoupper(substr($pending->member->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-family:'Fredoka One',cursive; font-size:0.8rem; color:var(--comic-dark);">
                                                {{ $pending->member->name }}
                                            </div>
                                            <div style="font-size:0.7rem; color:#aaa;">
                                                NIS: {{ $pending->member->nis_nim ?? '-' }} | {{ $pending->member->class }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-family:'Fredoka One',cursive; font-size:0.82rem; color:var(--comic-dark);">
                                        {{ $pending->details->count() }} buku
                                    </div>
                                    <div style="font-size:0.7rem; color:#aaa;">
                                        {{ $pending->details->take(2)->pluck('book.title')->implode(', ') }}
                                        @if($pending->details->count() > 2), +{{ $pending->details->count() - 2 }} lagi @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:0.75rem; color:#888;">{{ $pending->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('api.borrowings.approve', $pending->id) }}" method="POST" class="d-inline form-approve">
                                            @csrf
                                            <button type="submit" class="btn btn-sm fw-bold btn-approve"
                                                style="background:#27ae60; color:#fff; border:2px solid var(--comic-dark); box-shadow:2px 2px 0 var(--comic-dark); border-radius:0; font-size:0.72rem;"
                                                onclick="return confirm('Setujui peminjaman ini?');">
                                                ✅ Setuju
                                            </button>
                                        </form>
                                        <form action="{{ route('api.borrowings.reject', $pending->id) }}" method="POST" class="d-inline form-reject">
                                            @csrf
                                            <button type="submit" class="btn btn-sm fw-bold"
                                                style="background:var(--comic-red); color:#fff; border:2px solid var(--comic-dark); box-shadow:2px 2px 0 var(--comic-dark); border-radius:0; font-size:0.72rem;"
                                                onclick="return confirm('Tolak request ini?');">
                                                ❌ Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">✅ TIDAK ADA REQUEST PENDING</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Members at Library Now --}}
    <div class="col-lg-4">
        <div class="card dash-chart-card">
            <div class="card-header d-flex align-items-center justify-content-between" style="background: var(--comic-blue);">
                <div class="card-title" style="color: #fff !important;">
                    🟢 SISWA DI PERPUSTAKAAN
                </div>
                <span style="font-family:'Fredoka One',cursive; font-size:0.68rem; color:rgba(255,255,255,0.7); letter-spacing:1px;">
                    {{ $activeAttendances->count() }} orang
                </span>
            </div>
            <div class="card-body p-0" style="max-height:300px; overflow-y:auto;">
                @if($activeAttendances->count())
                    @foreach($activeAttendances as $attendance)
                    <div style="display:flex; align-items:center; gap:12px; padding:12px 16px; border-bottom:1px solid rgba(26,26,46,0.08);">
                        <div style="width:36px; height:36px; background:var(--comic-cream); border:2px solid var(--comic-blue);
                                    display:flex; align-items:center; justify-content:center; font-family:'Fredoka One',cursive; font-size:0.9rem; color:var(--comic-dark); flex-shrink:0;">
                            {{ strtoupper(substr($attendance->member->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-family:'Fredoka One',cursive; font-size:0.85rem; color:var(--comic-dark);">
                                {{ $attendance->member->name }}
                            </div>
                            <div style="font-size:0.7rem; color:#aaa;">
                                NIS: {{ $attendance->member->nis_nim ?? '-' }} | {{ $attendance->member->class }} | Scan: {{ $attendance->scanned_at->format('H:i') }}
                            </div>
                        </div>
                        <span class="badge-comic" style="background:var(--comic-blue); color:#fff;">🟢</span>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">👻 BELUM ADA SISWA</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Row 4: Quick Actions + Keterlambatan ────────────────────────── --}}
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card dash-chart-card">
            <div class="card-header">
                <div class="card-title">⚡ QUICK ACTIONS</div>
            </div>
            <div class="card-body">
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <a href="{{ route('admin.books.index') }}" class="quick-action-btn qab-primary">
                        <span class="qab-icon">📕</span>
                        <span class="qab-text">
                            <strong>Kelola Buku</strong>
                            <small>Tambah, edit, hapus koleksi</small>
                        </span>
                        <span class="qab-arrow">→</span>
                    </a>
                    <a href="{{ route('admin.members.index') }}" class="quick-action-btn qab-secondary">
                        <span class="qab-icon">👥</span>
                        <span class="qab-text">
                            <strong>Kelola Anggota</strong>
                            <small>Lihat & edit data member</small>
                        </span>
                        <span class="qab-arrow">→</span>
                    </a>
                    <a href="{{ route('admin.returns.index') }}" class="quick-action-btn qab-secondary">
                        <span class="qab-icon">📤</span>
                        <span class="qab-text">
                            <strong>Proses Peminjaman</strong>
                            <small>Pinjam buku untuk anggota</small>
                        </span>
                        <span class="qab-arrow">→</span>
                    </a>
                    <a href="{{ route('scan.kiosk') }}" class="quick-action-btn qab-accent" target="_blank">
                        <span class="qab-icon">🖥️</span>
                        <span class="qab-text">
                            <strong>Mode Kiosk</strong>
                            <small>Scan di counter perpustakaan</small>
                        </span>
                        <span class="qab-arrow">↗</span>
                    </a>
                    <a href="{{ route('admin.returns.index') }}" class="quick-action-btn qab-secondary">
                        <span class="qab-icon">📥</span>
                        <span class="qab-text">
                            <strong>Proses Pengembalian</strong>
                            <small>Return buku + hitung keterlambatan</small>
                        </span>
                        <span class="qab-arrow">→</span>
                    </a>
                    <a href="{{ route('admin.returns.scan') }}" class="quick-action-btn qab-accent">
                        <span class="qab-icon">📷</span>
                        <span class="qab-text">
                            <strong>Scan Return</strong>
                            <small>Scan QR return buku</small>
                        </span>
                        <span class="qab-arrow">→</span>
                    </a>
                    <a href="{{ route('admin.fines.index') }}" class="quick-action-btn qab-danger">
                        <span class="qab-icon">⏰</span>
                        <span class="qab-text">
                            <strong>Kelola Keterlambatan</strong>
                            <small>Riwayat keterlambatan peminjaman</small>
                        </span>
                        <span class="qab-arrow">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card dash-chart-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title">⏰ RIWAYAT KETERLAMBATAN</div>
                <a href="{{ route('admin.fines.index') }}"
                    style="background:var(--comic-yellow); color:var(--comic-dark); font-family:'Fredoka One',cursive;
                           font-size:0.68rem; border-radius:0; border:2px solid var(--comic-dark);
                           box-shadow:2px 2px 0 var(--comic-dark); padding:4px 10px; text-decoration:none;">
                    Lihat Semua →
                </a>
            </div>
            <div class="card-body p-0">
                @php
                    $recentFines = \App\Models\Fine::with(['borrowing.member', 'borrowing.details.book'])
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                @if($recentFines->count())
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>👤 ANGGOTA</th>
                                <th>📖 BUKU</th>
                                <th>⏰ HARI</th>
                                <th>⚡ STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentFines as $fine)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; background:var(--comic-yellow); border:2px solid var(--comic-dark);
                                                    display:flex; align-items:center; justify-content:center;
                                                    font-family:'Fredoka One',cursive; font-size:0.72rem; color:var(--comic-dark); flex-shrink:0;">
                                            {{ strtoupper(substr($fine->borrowing->member->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span style="font-family:'Fredoka One',cursive; font-size:0.8rem; color:var(--comic-dark); white-space:nowrap;">
                                            {{ $fine->borrowing->member->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @php $bookTitle = $fine->borrowing && $fine->borrowing->details->first()?->book?->title; @endphp
                                    <span style="font-size:0.78rem; color:#888;">{{ $bookTitle ? Str::limit($bookTitle, 20) : '-' }}</span>
                                </td>
                                <td>
                                    <span style="font-family:'Fredoka One',cursive; font-size:0.85rem; color:var(--comic-dark);">
                                        {{ $fine->days_late }} hari
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-comic" style="background:{{ $fine->status === 'paid' ? 'var(--comic-green)' : 'var(--comic-yellow)' }}; color:#fff;">
                                        {{ $fine->status === 'paid' ? 'LUNAS' : 'BELUM' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state" style="padding:24px; color:var(--comic-green);">
                    🎉 BELUM ADA DATA KETERLAMBATAN
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var gridColor = '#eee';

    // Chart 1: Bar — Peminjaman Per Bulan
    var mLabels = {!! json_encode($monthlyBorrowings->pluck('label')->toArray()) !!};
    var mData   = {!! json_encode($monthlyBorrowings->pluck('total')->toArray()) !!};

    if (document.getElementById('chartMonthly')) {
        new Chart(document.getElementById('chartMonthly'), {
            type: 'line',
            data: {
                labels: mLabels.length ? mLabels : ['Tidak ada data'],
                datasets: [{
                    data: mData.length ? mData : [0],
                    backgroundColor: '#FF6B35',
                    borderColor: '#1A1A2E',
                    borderWidth: 3,
                    borderRadius: 4,
                    borderSkipped: false,
                    tension: 0.4,
                    pointBackgroundColor: '#FF6B35',
                    pointBorderColor: '#1A1A2E',
                    pointBorderWidth: 2,
                    pointRadius: 5,
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
                        bodyFont:  { family: "'Nunito', sans-serif", weight: '700' },
                        padding: 8,
                        cornerRadius: 0,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Nunito', sans-serif", weight: '800', size: 9 }, color: '#999' },
                        border: { color: gridColor }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { font: { family: "'Nunito', sans-serif", weight: '800', size: 9 }, color: '#999', stepSize: 1 },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Chart 2: Doughnut — Status (fixed size 140x140)
    if (document.getElementById('chartStatus')) {
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [
                        {!! $borrowingStatusCounts['active'] !!},
                        {!! $borrowingStatusCounts['late'] !!},
                        {!! $borrowingStatusCounts['returned'] !!}
                    ],
                    backgroundColor: ['#4ECDC4', '#FF3366', '#2ecc71'],
                    borderColor: '#fff',
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                width: 130,
                height: 130,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1A1A2E',
                        titleFont: { family: "'Nunito', sans-serif", weight: '800' },
                        bodyFont:  { family: "'Nunito', sans-serif", weight: '700' },
                        padding: 8,
                        cornerRadius: 0,
                    }
                }
            }
        });
    }

    // Chart 3: Horizontal Bar — Kategori
    var catLabels = {!! json_encode($categoryBooks->pluck('name')->toArray()) !!};
    var catData   = {!! json_encode($categoryBooks->pluck('count')->toArray()) !!};

    if (document.getElementById('chartCategory') && catLabels.length) {
        new Chart(document.getElementById('chartCategory'), {
            type: 'bar',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: '#FF6B35',
                    borderColor: '#1A1A2E',
                    borderWidth: 2,
                    borderRadius: 0,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1A1A2E',
                        titleFont: { family: "'Nunito', sans-serif", weight: '800' },
                        bodyFont:  { family: "'Nunito', sans-serif", weight: '700' },
                        padding: 8,
                        cornerRadius: 0,
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { font: { family: "'Nunito', sans-serif", weight: '800', size: 9 }, color: '#999', stepSize: 1 },
                        border: { display: false }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { family: "'Nunito', sans-serif", weight: '800', size: 10 }, color: '#666' },
                        border: { color: gridColor }
                    }
                }
            }
        });
    }
});
</script>
@endpush