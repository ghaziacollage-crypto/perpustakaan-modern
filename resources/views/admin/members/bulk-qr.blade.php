@extends('layouts.app')

@section('title', 'Sinkronisasi QR Code')
@section('page-title', 'Sinkronisasi QR Code Anggota')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.members.index') }}" class="text-muted text-hover-primary">Anggota</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Sinkronisasi QR</li>
</ul>
@endsection

@push('custom-css')
<style>
:root {
    --comic-dark:    #1A1A2E;
    --comic-orange: #FF6B35;
    --comic-yellow:  #FFE66D;
    --comic-blue:    #4ECDC4;
    --comic-red:     #FF3366;
    --comic-green:   #00C896;
    --comic-cream:   #FFF8F0;
    --shadow-sm:     2px 2px 0 var(--comic-dark);
    --shadow-md:     4px 4px 0 var(--comic-dark);
    --shadow-lg:     6px 6px 0 var(--comic-dark);
    --border-comic:  3px solid var(--comic-dark);
    --font-title:    'Bangers', cursive;
    --font-body:     'Fredoka One', cursive;
}
.comic-stat {
    border: var(--border-comic);
    box-shadow: var(--shadow-md);
    background: #fff;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
}
.comic-stat .stat-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.65rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #aaa;
}
.comic-stat .stat-value {
    font-family: var(--font-title);
    font-size: 2.2rem;
    line-height: 1;
    text-shadow: 2px 2px 0 rgba(0,0,0,0.1);
}
.comic-stat .stat-icon { font-size: 2rem; }
.btn-qr-action {
    font-family: var(--font-title);
    letter-spacing: 2px;
    font-size: 1rem;
    border: 3px solid var(--comic-dark);
    box-shadow: var(--shadow-sm);
    padding: 10px 24px;
    border-radius: 0;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-qr-action:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.btn-qr-regen {
    background: var(--comic-orange);
    color: #fff;
}
.btn-qr-gen {
    background: var(--comic-green);
    color: #fff;
}
.btn-qr-back {
    background: var(--comic-cream);
    color: var(--comic-dark);
}
</style>
@endpush

@section('content')
{{-- Summary Cards --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-blue) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">👥</div>
                <div>
                    <div class="stat-label">TOTAL ANGGOTA</div>
                    <div class="stat-value" style="color:var(--comic-blue);">{{ number_format($totalMembers) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-green) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">✅</div>
                <div>
                    <div class="stat-label">SUDAH PUNYA QR</div>
                    <div class="stat-value" style="color:var(--comic-green);">{{ number_format($membersWithQr) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="comic-stat" style="border-top:5px solid var(--comic-yellow) !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">⚠️</div>
                <div>
                    <div class="stat-label">BELUM PUNYA QR</div>
                    <div class="stat-value" style="color:#b07d00;">{{ number_format($membersWithoutQr) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-0 pt-6" style="background: var(--comic-dark) !important;">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📱 SINKRONISASI QR CODE ANGGOTA</span>
        </div>
    </div>
    <div class="card-body p-5">
        <div class="row g-4">
            <div class="col-12">
                <div style="border:var(--border-comic);box-shadow:var(--shadow-md);padding:24px;background:var(--comic-cream);">
                    <h5 style="font-family:var(--font-title);letter-spacing:2px;color:var(--comic-dark);margin-bottom:12px;">
                        🔁 REGENERATE SEMUA QR CODE
                    </h5>
                    <p style="font-family:var(--font-body);font-size:0.82rem;color:#888;margin-bottom:16px;">
                        Menghasilkan ulang QR Code untuk <strong>semua {{ number_format($totalMembers) }} anggota</strong>.
                        QR lama akan dihapus dan diganti dengan yang baru. Proses ini akan memproses anggota
                        yang sudah memiliki QR ({{ number_format($membersWithQr) }}) maupun yang belum
                        ({{ number_format($membersWithoutQr) }}).
                    </p>
                    <form action="{{ route('admin.members.bulk-qr-regenerate') }}" method="POST"
                          onsubmit="return confirm('Regenerate semua QR Code {{ number_format($totalMembers) }} anggota?\n\nIni akan menghapus QR lama dan membuat QR baru.');">
                        @csrf
                        <button type="submit" class="btn-qr-action btn-qr-regen w-100">
                            <i class="ki-duotone ki-arrows-circle me-2"></i> REGENERATE SEMUA QR
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.members.index') }}" class="btn-qr-action btn-qr-back text-decoration-none">
                <i class="ki-duotone ki-arrow-left me-2"></i> KEMBALI KE DAFTAR ANGGOTA
            </a>
        </div>
    </div>
</div>
@endsection
