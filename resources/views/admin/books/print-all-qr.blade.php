@extends('layouts.print')

@section('title', 'Print Semua QR Code')

@push('custom-css')
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #fff; padding: 20px; font-family: sans-serif; }
    .header {
        background: #1A1A2E;
        color: #fff;
        padding: 16px 24px;
        border: 3px solid #1A1A2E;
        box-shadow: 4px 4px 0 #FF6B35;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header h1 {
        font-family: 'Bangers', cursive;
        font-size: 1.5rem;
        letter-spacing: 2px;
        color: #FF6B35;
    }
    .header .meta {
        font-family: 'Fredoka One', cursive;
        font-size: 0.7rem;
        color: rgba(255,255,255,0.6);
        letter-spacing: 1px;
    }
    .btn-print {
        background: #FF6B35;
        color: #fff;
        border: 2px solid #1A1A2E;
        box-shadow: 3px 3px 0 #1A1A2E;
        padding: 8px 18px;
        font-family: 'Bangers', cursive;
        font-size: 0.9rem;
        letter-spacing: 1px;
        cursor: pointer;
        border-radius: 0;
    }
    .btn-print:hover { background: #FFE66D; color: #1A1A2E; }
    .no-books {
        text-align: center;
        padding: 60px;
        font-family: 'Bangers', cursive;
        font-size: 1.4rem;
        color: #1A1A2E;
        letter-spacing: 2px;
    }
    .qr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 16px;
    }
    .qr-card {
        border: 3px solid #1A1A2E;
        padding: 14px;
        background: #fff;
        box-shadow: 4px 4px 0 #1A1A2E;
        display: flex;
        align-items: center;
        gap: 14px;
        page-break-inside: avoid;
    }
    .qr-card .qr-img {
        width: 100px;
        height: 100px;
        object-fit: contain;
        flex-shrink: 0;
        border: 2px solid #1A1A2E;
    }
    .qr-card .info {
        flex: 1;
        overflow: hidden;
    }
    .qr-card .info .title {
        font-family: 'Fredoka One', cursive;
        font-size: 0.9rem;
        color: #1A1A2E;
        line-height: 1.3;
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .qr-card .info .row {
        display: flex;
        gap: 6px;
        margin: 3px 0;
    }
    .qr-card .info .lbl {
        font-family: 'Fredoka One', cursive;
        font-size: 0.58rem;
        color: #aaa;
        letter-spacing: 1px;
        text-transform: uppercase;
        min-width: 48px;
    }
    .qr-card .info .val {
        font-family: 'Fredoka One', cursive;
        font-size: 0.72rem;
        color: #1A1A2E;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .summary {
        font-family: 'Fredoka One', cursive;
        font-size: 0.72rem;
        color: rgba(255,255,255,0.5);
        letter-spacing: 1px;
    }
    @media print {
        body { padding: 0; }
        .btn-print { display: none !important; }
        .header { box-shadow: none; border-width: 2px; }
        .qr-card { box-shadow: none; border-width: 2px; }
    }
</style>
@endpush

@section('content')
<div style="text-align:center; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
    <h1 style="font-family:'Bangers',cursive; font-size:1.5rem; letter-spacing:2px; color:#1A1A2E;">📚 DAFTAR QR CODE BUKU</h1>
    <div style="display:flex;align-items:center;gap:12px;">
        <div class="summary" style="font-family:'Fredoka One',cursive; font-size:0.72rem; color:#888;">Total: {{ $books->count() }} buku &bull; {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</div>
        <button class="btn-print" onclick="window.print()">🖨️ PRINT QR CODES</button>
    </div>
</div>

@if($books->isEmpty())
    <div class="no-books">
        <div style="font-size:4rem;margin-bottom:16px;">📭</div>
        <div>TIDAK ADA QR CODE UNTUK DI-PRINT</div>
        <div style="font-size:0.8rem;color:#aaa;margin-top:8px;font-family:sans-serif;">Semua buku belum memiliki QR Code</div>
    </div>
@else
    <div class="qr-grid">
        @foreach($books as $book)
        <div class="qr-card">
            <img src="{{ asset('storage/' . $book->qr_code) }}"
                alt="QR-{{ $book->book_code }}"
                class="qr-img"/>
            <div class="info">
                <div class="title" title="{{ $book->title }}">{{ $book->title }}</div>
                <div class="row">
                    <span class="lbl">KODE</span>
                    <span class="val">{{ $book->book_code }}</span>
                </div>
                <div class="row">
                    <span class="lbl">ISBN</span>
                    <span class="val">{{ $book->isbn ?? '-' }}</span>
                </div>
                <div class="row">
                    <span class="lbl">PENULIS</span>
                    <span class="val">{{ Str::limit($book->author ?? '-', 20) }}</span>
                </div>
                <div class="row">
                    <span class="lbl">RAK</span>
                    <span class="val">{{ $book->rack_location ?? '-' }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection