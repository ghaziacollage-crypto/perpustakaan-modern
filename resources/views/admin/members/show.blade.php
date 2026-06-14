@extends('layouts.app')

@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Manajemen</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.members.index') }}" class="text-muted text-hover-primary">Data Anggota</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">{{ $member->name }}</li>
</ul>
@endsection

@push('custom-css')
<style>
/* ── Member Detail Page ── */
.member-detail-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 20px;
    align-items: start;
}

/* ── ID Card ── */
.id-card-wrap {
    position: sticky;
    top: 20px;
}
.id-card {
    border: 3px solid var(--comic-dark);
    box-shadow: 5px 5px 0 var(--comic-dark);
    border-radius: 0;
    overflow: hidden;
    background: linear-gradient(135deg, var(--comic-dark) 0%, #2d2d4a 100%);
}
.id-card-header {
    background: var(--comic-orange);
    border-bottom: 3px solid var(--comic-dark);
    padding: 12px 16px;
    position: relative;
}
.id-card-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: repeating-linear-gradient(90deg, var(--comic-dark) 0px, var(--comic-dark) 8px, transparent 8px, transparent 16px);
}
.id-card-header .card-org {
    font-family: 'Bangers', cursive;
    color: #fff;
    font-size: 0.9rem;
    letter-spacing: 2px;
    line-height: 1;
    text-shadow: 1px 1px 0 rgba(0,0,0,0.3);
}
.id-card-header .card-subtitle {
    font-family: 'Fredoka One', cursive;
    color: rgba(255,255,255,0.75);
    font-size: 0.6rem;
    letter-spacing: 1px;
    margin-top: 2px;
}
.id-card-body {
    padding: 14px 16px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    background: var(--comic-cream);
}
.id-card-photo {
    width: 80px;
    height: 96px;
    flex-shrink: 0;
    border: 3px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    overflow: hidden;
    background: var(--comic-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.id-card-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.id-card-photo .photo-placeholder {
    font-size: 2rem;
    color: rgba(255,255,255,0.5);
}
.id-card-photo:hover .photo-overlay {
    opacity: 1;
}
.photo-overlay {
    position: absolute;
    inset: 0;
    background: rgba(26,26,46,0.72);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    opacity: 0;
    transition: opacity 0.2s;
    cursor: pointer;
}
.photo-overlay span {
    font-family: 'Fredoka One', cursive;
    font-size: 0.55rem;
    color: #fff;
    letter-spacing: 1px;
    text-align: center;
    line-height: 1.3;
}
.id-card-info {
    flex: 1;
    min-width: 0;
}
.id-card-info .card-name {
    font-family: 'Bangers', cursive;
    color: var(--comic-dark);
    font-size: 1.05rem;
    letter-spacing: 1px;
    line-height: 1.1;
    margin-bottom: 6px;
    word-break: break-word;
}
.id-card-info .card-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.id-card-info .card-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
}
.id-card-info .card-meta-icon {
    font-size: 0.75rem;
    flex-shrink: 0;
}
.id-card-info .card-meta-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.58rem;
    color: #aaa;
    letter-spacing: 1px;
    text-transform: uppercase;
    line-height: 1;
}
.id-card-info .card-meta-value {
    font-family: 'Fredoka One', cursive;
    font-size: 0.72rem;
    color: var(--comic-dark);
    font-weight: 700;
    line-height: 1.2;
    word-break: break-word;
}
.id-card-footer {
    background: var(--comic-cream);
    border-top: 2px dashed rgba(26,26,46,0.2);
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.id-card-qr {
    flex-shrink: 0;
}
.id-card-qr img {
    width: 68px;
    height: 68px;
    border: 2px solid var(--comic-dark);
    box-shadow: 2px 2px 0 var(--comic-dark);
}
.id-card-footer-info {
    flex: 1;
    text-align: right;
}
.id-card-code {
    font-family: 'Bangers', cursive;
    font-size: 1rem;
    color: var(--comic-dark);
    letter-spacing: 2px;
}
.id-card-status {
    font-family: 'Fredoka One', cursive;
    font-size: 0.62rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.status-active { color: var(--comic-green); }
.status-inactive { color: var(--comic-red); }

/* ── Print Button ── */
.print-card-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    margin-top: 10px;
    background: var(--comic-orange) !important;
    color: #fff !important;
    border: 2px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    box-shadow: 3px 3px 0 var(--comic-dark) !important;
    font-family: 'Fredoka One', cursive !important;
    font-size: 0.82rem !important;
    letter-spacing: 1px;
    font-weight: 900 !important;
    padding: 10px 16px !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.print-card-btn:hover {
    background: var(--comic-yellow) !important;
    color: var(--comic-dark) !important;
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark) !important;
}

/* ── Stats Row ── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.stat-box {
    border: 3px solid var(--comic-dark);
    box-shadow: 4px 4px 0 var(--comic-dark);
    border-top: 5px solid var(--comic-orange);
    background: #fff;
    padding: 14px 16px;
    text-align: center;
}
.stat-box .stat-icon { font-size: 1.6rem; }
.stat-box .stat-value {
    font-family: 'Bangers', cursive;
    font-size: 1.8rem;
    color: var(--comic-dark);
    line-height: 1;
    margin-top: 4px;
}
.stat-box .stat-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.6rem;
    color: #aaa;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 4px;
}

/* ── Info Cards ── */
.info-section {
    border: 3px solid var(--comic-dark);
    box-shadow: 4px 4px 0 var(--comic-dark);
    background: #fff;
    margin-bottom: 16px;
}
.info-section-header {
    background: var(--comic-dark);
    border-bottom: 3px solid var(--comic-orange);
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.info-section-header .section-title {
    font-family: 'Bangers', cursive;
    color: var(--comic-orange);
    font-size: 0.95rem;
    letter-spacing: 2px;
}
.info-section-body {
    padding: 14px 16px;
}
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
.info-item { display: flex; flex-direction: column; gap: 3px; }
.info-item .info-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.62rem;
    color: #aaa;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.info-item .info-value {
    font-family: 'Fredoka One', cursive;
    font-size: 0.85rem;
    color: var(--comic-dark);
    font-weight: 700;
    word-break: break-word;
}
.info-item .info-value.mono {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}
.info-item.full-width { grid-column: 1 / -1; }

/* ── Table override ── */
.mini-table { width: 100%; border-collapse: collapse; }
.mini-table thead tr {
    border-bottom: 2px solid var(--comic-dark);
}
.mini-table thead tr th {
    font-family: 'Fredoka One', cursive;
    font-size: 0.62rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #888;
    padding: 8px 10px;
    font-weight: 700;
    text-align: left;
}
.mini-table tbody tr {
    border-bottom: 1px solid rgba(26,26,46,0.07);
}
.mini-table tbody tr:last-child { border-bottom: none; }
.mini-table tbody tr:hover { background: rgba(255,107,53,0.06); }
.mini-table tbody tr td {
    padding: 8px 10px;
    font-size: 0.82rem;
    color: var(--comic-dark);
    vertical-align: middle;
}
.badge-mini {
    display: inline-block;
    font-family: 'Fredoka One', cursive;
    font-size: 0.6rem;
    letter-spacing: 1px;
    padding: 3px 8px;
    border-radius: 0;
    border: 2px solid var(--comic-dark);
}
@media (max-width: 768px) {
    .member-detail-grid { grid-template-columns: 1fr; }
    .id-card-wrap { position: static; }
    .stats-row { grid-template-columns: 1fr 1fr; }
    .info-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
{{-- Stats Row ─────────────────────────────────────────────────── --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon">📖</div>
        <div class="stat-value">{{ number_format($member->borrowings_count) }}</div>
        <div class="stat-label">Total Peminjaman</div>
    </div>
    <div class="stat-box" style="border-top-color: var(--comic-blue);">
        <div class="stat-icon">📤</div>
        <div class="stat-value">{{ number_format($member->active_borrowings_count) }}</div>
        <div class="stat-label">Sedang Dipinjam</div>
    </div>
    <div class="stat-box" style="border-top-color: var(--comic-red);">
        <div class="stat-icon">⏰</div>
        <div class="stat-value">{{ number_format($member->fines_count) }}</div>
        <div class="stat-label">Total Keterlambatan</div>
    </div>
</div>

{{-- Main Grid ──────────────────────────────────────────────────── --}}
<div class="member-detail-grid">
    {{-- Left: ID Card ─────────────────────────────────────────── --}}
    <div class="id-card-wrap">
        <div class="id-card">
            {{-- Header --}}
            <div class="id-card-header">
                <div class="card-org">{{ app_setting('app_name', 'PERPUSTAKAAN SEKOLAH') }}</div>
                <div class="card-subtitle">KARTU ANGGOTA • {{ app_setting('app_tagline', 'Perpustakaan Digital') }}</div>
            </div>

            {{-- Body: Photo + Info --}}
            <div class="id-card-body">
                <div class="id-card-photo">
                    @if($member->photo)
                        <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}"/>
                        <div class="photo-overlay" onclick="window.open('{{ asset('storage/' . $member->photo) }}', '_blank')">
                            <span>🔍</span>
                            <span>LIHAT FOTO</span>
                        </div>
                    @else
                        <span class="photo-placeholder">👤</span>
                        <div class="photo-overlay" style="opacity:1; background:rgba(26,26,46,0.5);">
                            <span style="font-family:'Fredoka One',cursive; font-size:0.6rem; color:rgba(255,255,255,0.6);">Belum ada foto</span>
                        </div>
                    @endif
                </div>
                <div class="id-card-info">
                    <div class="card-name">{{ $member->name }}</div>
                    <div class="card-meta">
                        <div class="card-meta-row">
                            <span class="card-meta-icon">🏫</span>
                            <div>
                                <div class="card-meta-label">Kelas</div>
                                <div class="card-meta-value">{{ $member->class ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="card-meta-row">
                            <span class="card-meta-icon">📚</span>
                            <div>
                                <div class="card-meta-label">Jurusan</div>
                                <div class="card-meta-value">{{ $member->major ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="card-meta-row">
                            <span class="card-meta-icon">🔢</span>
                            <div>
                                <div class="card-meta-label">NIS/NIM</div>
                                <div class="card-meta-value">{{ $member->nis_nim ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer: QR + Code --}}
            <div class="id-card-footer">
                <div class="id-card-qr">
                    @if($member->qr_code)
                        <img src="{{ asset('storage/' . $member->qr_code) }}" alt="QR Code"/>
                    @else
                        <div style="width:68px; height:68px; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; font-size:0.7rem; color:#aaa; text-align:center;">QR<br>Belum ada</div>
                    @endif
                </div>
                <div class="id-card-footer-info">
                    <div class="id-card-code">{{ $member->member_code }}</div>
                    <div class="id-card-status {{ $member->status->value === 'active' ? 'status-active' : 'status-inactive' }}">
                        {{ $member->status->value === 'active' ? '✓ AKTIF' : '✕ NONAKTIF' }}
                    </div>
                </div>
            </div>
        </div>

        <button onclick="window.open('{{ route('admin.members.print-card', $member) }}', '_blank')" class="print-card-btn">
            <i class="ki-duotone ki-printer fs-4"></i> CETAK KARTU
        </button>
    </div>

    {{-- Right: Info + Tables ─────────────────────────────────── --}}
    <div>
        {{-- Info Section: Data Diri --}}
        <div class="info-section">
            <div class="info-section-header">
                <span class="section-title">👤 DATA DIRI ANGGOTA</span>
                <a href="{{ route('admin.members.index') }}" class="btn btn-sm" style="background:var(--comic-orange); color:#fff; border-radius:0; border:2px solid var(--comic-dark); box-shadow:2px 2px 0 var(--comic-dark); font-family:'Fredoka One',cursive; font-size:0.72rem;">
                    ← Kembali
                </a>
            </div>
            <div class="info-section-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value">{{ $member->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kode Anggota</span>
                        <span class="info-value mono">{{ $member->member_code }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">NIS / NIM</span>
                        <span class="info-value">{{ $member->nis_nim ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kelas</span>
                        <span class="info-value">{{ $member->class ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jurusan</span>
                        <span class="info-value">{{ $member->major ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="badge-mini" style="background:{{ $member->status->value === 'active' ? 'var(--comic-green)' : '#ccc' }}; color:#fff; width:fit-content;">
                            {{ ucfirst($member->status->value) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">WhatsApp</span>
                        <span class="info-value">{{ $member->whatsapp ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value" style="font-size:0.78rem;">{{ $member->email ?? '-' }}</span>
                    </div>
                    <div class="info-item full-width">
                        <span class="info-label">Alamat</span>
                        <span class="info-value">{{ $member->address ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Section: Riwayat Peminjaman --}}
        @if($recentBorrowings->count())
        <div class="info-section">
            <div class="info-section-header">
                <span class="section-title">📋 RIWAYAT PEMINJAMAN TERBARU</span>
                <a href="{{ route('admin.borrowings.index', ['member' => $member->id]) }}"
                    style="background:var(--comic-dark); color:var(--comic-orange); border-radius:0; border:2px solid var(--comic-dark);
                           box-shadow:2px 2px 0 var(--comic-orange); font-family:'Fredoka One',cursive; font-size:0.72rem;
                           padding:4px 12px; text-decoration:none;">
                    Lihat Semua →
                </a>
            </div>
            <div class="info-section-body" style="padding:0;">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>KODE</th>
                            <th>BUKU</th>
                            <th>TGL PINJAM</th>
                            <th>JATUH TEMPO</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBorrowings as $borrowing)
                        <tr>
                            <td style="font-family:'Fredoka One',cursive; font-size:0.75rem; color:#aaa;">{{ $borrowing->transaction_code }}</td>
                            <td>
                                @php $book = $borrowing->details->first()?->book; @endphp
                                <span style="font-size:0.8rem;">{{ $book ? Str::limit($book->title, 25) : '-' }}</span>
                            </td>
                            <td style="font-size:0.78rem; color:#888;">{{ $borrowing->loan_date->format('d M Y') }}</td>
                            <td style="font-size:0.78rem; {{ $borrowing->status->value === 'late' ? 'color:var(--comic-red);' : 'color:#888;' }}">
                                {{ $borrowing->due_date->format('d M Y') }}
                            </td>
                            <td>
                                @php
                                    $statusMap = [
                                        'active' => ['bg' => '#3498db', 'label' => 'AKTIF'],
                                        'late' => ['bg' => 'var(--comic-red)', 'label' => 'TERLAMBAT'],
                                        'returned' => ['bg' => 'var(--comic-green)', 'label' => 'KEMBALI'],
                                    ];
                                    $s = $statusMap[$borrowing->status->value] ?? ['bg' => '#aaa', 'label' => strtoupper($borrowing->status->value)];
                                @endphp
                                <span class="badge-mini" style="background:{{ $s['bg'] }}; color:#fff;">
                                    {{ $s['label'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection