@extends('layouts.app')

@section('title', 'QR Code — ' . $book->title)
@section('page-title', 'QR Code Buku')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.books.index') }}" class="text-muted text-hover-primary">Data Buku</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.books.show', $book) }}" class="text-muted text-hover-primary">{{ Str::limit($book->title, 20) }}</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">QR Code</li>
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
.qr-hero {
    text-align: center;
    padding: 40px 20px;
}
.qr-hero .qr-img {
    max-width: 320px;
    width: 100%;
    border: var(--border-comic);
    box-shadow: var(--shadow-lg);
    padding: 16px;
    background: var(--comic-cream);
}
.qr-hero .qr-label {
    font-family: var(--font-title);
    font-size: 1.1rem;
    letter-spacing: 3px;
    color: var(--comic-dark);
    margin-top: 16px;
}
.info-card {
    border: var(--border-comic);
    box-shadow: var(--shadow-md);
    background: #fff;
    padding: 20px;
    margin-top: 20px;
}
.info-row {
    display: flex;
    gap: 10px;
    margin: 6px 0;
    font-family: var(--font-body);
}
.info-row .lbl {
    color: #aaa;
    font-size: 0.75rem;
    min-width: 100px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.info-row .val {
    color: var(--comic-dark);
    font-weight: 900;
}
.qr-title {
    font-family: var(--font-title);
    font-size: 2rem;
    color: var(--comic-dark);
    letter-spacing: 2px;
    margin-bottom: 4px;
}
.qr-subtitle {
    font-family: var(--font-body);
    font-size: 0.8rem;
    color: #aaa;
    letter-spacing: 1px;
}
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
.btn-qr-print {
    background: var(--comic-orange);
    color: #fff;
}
.btn-qr-back {
    background: var(--comic-cream);
    color: var(--comic-dark);
}
.btn-qr-generate {
    background: var(--comic-green);
    color: #fff;
}
.qr-empty-state {
    text-align: center;
    padding: 40px 20px;
    font-family: var(--font-title);
    font-size: 1.3rem;
    color: var(--comic-dark);
    letter-spacing: 2px;
}
</style>
@endpush

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ asset('kop.png') }}" alt="Kop Surat" style="max-width:100%; height:auto;" />
</div>
<div class="card mb-5">
    <div class="card-header border-0 pt-6" style="background: var(--comic-dark) !important;">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.3rem;">📱 QR CODE BUKU</span>
        </div>
    </div>
    <div class="card-body p-5">
        <div class="qr-hero">
            @if($book->qr_code)
                <img src="{{ asset('storage/' . $book->qr_code) }}"
                    alt="QR Code — {{ $book->title }}"
                    class="qr-img"/>
                <div class="qr-label">SCAN UNTUK DETAIL BUKU</div>
            @else
                <div class="qr-empty-state">
                    <div style="font-size:4rem; margin-bottom:16px;">📭</div>
                    <div>QR Code belum tersedia untuk buku ini</div>
                </div>
                <form method="POST" action="{{ route('admin.books.regenerate-qr', $book) }}" class="mt-4 d-inline-block">
                    @csrf
                    <button type="submit" class="btn-qr-action btn-qr-generate">
                        <i class="ki-duotone ki-qrcode fs-5"></i> GENERATE QR CODE
                    </button>
                </form>
            @endif
        </div>

        @if($book->qr_code)
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="info-card">
                    <div class="qr-title">{{ $book->title }}</div>
                    <div class="qr-subtitle" style="margin-bottom:16px;">{{ $book->book_code }}</div>
                    <div class="info-row">
                        <span class="lbl">ISBN</span>
                        <span class="val">{{ $book->isbn ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Penulis</span>
                        <span class="val">{{ $book->author ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Kategori</span>
                        <span class="val">{{ $book->category?->name ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Lokasi Rak</span>
                        <span class="val">{{ $book->rack_location ?? '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex align-items-start">
                <div class="d-flex flex-column gap-3 w-100">
                    <button onclick="printBookQr()" class="btn-qr-action btn-qr-print w-100">
                        <i class="ki-duotone ki-printer me-2"></i> PRINT QR CODE
                    </button>
                    <form method="POST" action="{{ route('admin.books.regenerate-qr', $book) }}">
                        @csrf
                        <button type="submit" class="btn-qr-action w-100" style="background:var(--comic-yellow);color:var(--comic-dark);">
                            <i class="ki-duotone ki-refresh me-2"></i> REGENERATE QR
                        </button>
                    </form>
                    <a href="{{ route('admin.books.show', $book) }}" class="btn-qr-action btn-qr-back w-100 text-center text-decoration-none">
                        <i class="ki-duotone ki-arrow-left me-2"></i> KEMBALI
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<div style="text-align: center; margin-top: 40px; page-break-inside: avoid;">
    <div>Kepala Perpustakaan</div>
    <div style="height:60px;"></div>
    <div style="font-weight:bold; text-decoration:underline;">
        Ailen Rossa Nauda, M.Pd.
    </div>
    <div>NIP. 196904061998022001</div>
</div>
@endsection

@push('custom-js')
<script>
function printBookQr() {
    var qrSrc = document.querySelector('.qr-img')?.src || '';
    if (!qrSrc) return;

    var title    = {{ Js::from($book->title) }};
    var code     = {{ Js::from($book->book_code) }};
    var isbn     = {{ Js::from($book->isbn ?? '-') }};
    var author   = {{ Js::from($book->author ?? '-') }};
    var category = {{ Js::from($book->category?->name ?? '-') }};

    var html = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<title>Print QR — ${title}</title>
<link href="https://fonts.googleapis.com/css2?family=Bangers&family=Fredoka+One&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Fredoka One',sans-serif;padding:24px;background:#fff;}
.kop{text-align:center;margin-bottom:20px;}
.kop img{max-width:100%;height:auto;}
#wrap{display:flex;align-items:center;gap:20px;border:3px solid #1A1A2E;padding:20px;box-shadow:6px 6px 0 #1A1A2E;background:#fff;}
#wrap img{width:160px;height:160px;object-fit:contain;}
.info{border-left:3px solid #1A1A2E;padding-left:20px;flex:1;}
.title{font-family:'Bangers',sans-serif;font-size:22px;color:#1A1A2E;margin-bottom:12px;letter-spacing:2px;}
.row{display:flex;gap:10px;margin:5px 0;font-size:13px;}
.lbl{color:#aaa;font-size:11px;min-width:60px;text-transform:uppercase;}
.val{color:#1A1A2E;font-weight:900;}
.signature{text-align:center;margin-top:40px;}
.signature .title{font-weight:bold;}
@media print{body{padding:0;}}
</style>
</head>
<body>
<div class="kop"><img src="{{ asset('kop.png') }}" alt="Kop Surat"/></div>
<div id="wrap">
    <img src="${qrSrc}" alt="QR"/>
    <div class="info">
        <div class="title">${title}</div>
        <div class="row"><span class="lbl">KODE</span><span class="val">${code}</span></div>
        <div class="row"><span class="lbl">ISBN</span><span class="val">${isbn}</span></div>
        <div class="row"><span class="lbl">PENULIS</span><span class="val">${author}</span></div>
        <div class="row"><span class="lbl">KATEGORI</span><span class="val">${category}</span></div>
    </div>
</div>
<div class="signature">
    <div>Kepala Perpustakaan</div>
    <div style="height:60px;"></div>
    <div class="title" style="font-weight:bold;text-decoration:underline;">Ailen Rossa Nauda, M.Pd.</div>
    <div>NIP. 196904061998022001</div>
</div>
<script>window.onload=function(){window.print();}<\/script>
</body></html>`;

    var win = window.open('', '_blank', 'width=650,height=350');
    if (win) { win.document.write(html); win.document.close(); }
}
</script>
@endpush
