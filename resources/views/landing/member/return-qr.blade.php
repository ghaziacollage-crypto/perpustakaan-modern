@extends('landing.layout')

@section('title', 'Return QR — ' . $borrowing->transaction_code)

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-icon">📚</span>
            <span class="brand-text fw-black">{{ app_setting('app_name', 'Perpustakaan') }}</span>
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('member.borrowings', ['code' => $member->member_code]) }}"
               class="btn btn-outline-light btn-sm fw-bold" style="border-radius:0;">
                ← Kembali
            </a>
        </div>
    </div>
</nav>

{{-- Header --}}
<div class="detail-hero" style="min-height:15vh;">
    <div class="container">
        <div class="text-center text-white">
            <div class="section-label" style="color:var(--comic-yellow);">PENGEMBALIAN</div>
            <h1 class="comic-section-title text-white">📤 KODE RETURN <span class="text-orange">{{ $borrowing->transaction_code }}</span></h1>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Borrowing Info --}}
            <div class="card mb-4" style="border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark);">
                <div class="card-header" style="background:var(--comic-dark); border-bottom:4px solid var(--comic-orange);">
                    <div style="font-family:'Bangers',cursive; color:var(--comic-orange); letter-spacing:2px; font-size:1.1rem;">
                        📋 INFO PEMINJAMAN
                    </div>
                </div>
                <div class="card-body" style="background:var(--comic-cream);">
                    <div class="row g-3">
                        <div class="col-6 col-md-3 text-center">
                            <div style="font-family:'Bangers',cursive; font-size:1.8rem; color:var(--comic-orange);">{{ $borrowing->details->count() }}</div>
                            <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">BUKU</div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:var(--comic-dark);">{{ $borrowing->loan_date->format('d M Y') }}</div>
                            <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">TGL PINJAM</div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:{{ $borrowing->isOverdue() ? 'var(--comic-red)' : 'var(--comic-dark)' }};">
                                {{ $borrowing->due_date->format('d M Y') }}
                            </div>
                            <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">JATUH TEMPO</div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            @if($borrowing->isOverdue())
                                <div style="font-family:'Bangers',cursive; font-size:1.8rem; color:var(--comic-red);">{{ $borrowing->daysOverdue() }} Hari</div>
                                <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">TERLAMBAT</div>
                            @else
                                <div style="font-family:'Bangers',cursive; font-size:1.8rem; color:var(--comic-blue);">{{ (int) max(0, now()->diffInDays($borrowing->due_date)) }} Hari</div>
                                <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">SISA HARI</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Books List --}}
            <div class="card mb-4" style="border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark);">
                <div class="card-header" style="background:var(--comic-dark); border-bottom:4px solid var(--comic-orange);">
                    <div style="font-family:'Bangers',cursive; color:var(--comic-orange); letter-spacing:2px; font-size:1.1rem;">
                        📕 DAFTAR BUKU YANG DIKEMBALIKAN
                    </div>
                </div>
                <div class="card-body" style="background:#fff; padding:0;">
                    @foreach($borrowing->details as $detail)
                    <div style="display:flex; align-items:center; gap:12px; padding:12px 20px; border-bottom:1px solid #eee;">
                        @if($detail->book->cover)
                            <img src="{{ asset('storage/' . $detail->book->cover) }}" style="width:40px; height:55px; object-fit:cover; border:2px solid var(--comic-dark); flex-shrink:0;">
                        @endif
                        <div>
                            <div style="font-family:'Fredoka One',cursive; font-size:0.9rem; color:var(--comic-dark);">{{ $detail->book->title }}</div>
                            <div style="font-size:0.75rem; color:#888; font-weight:700;">{{ $detail->book->author }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Return QR Code --}}
            <div class="card" style="border:4px solid var(--comic-orange); box-shadow:6px 6px 0 var(--comic-orange); background:#fff;">
                <div class="card-header text-center" style="background:var(--comic-orange); border-bottom:4px solid var(--comic-dark);">
                    <div style="font-family:'Bangers',cursive; color:#fff; letter-spacing:2px; font-size:1.3rem;">
                        📤 SCAN INI UNTUK VERIFIKASI RETURN
                    </div>
                </div>
                <div class="card-body text-center py-5" style="background:var(--comic-cream);">
                    <div style="display:inline-block; padding:20px; background:#fff; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark);">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->margin(2)->backgroundColor(255,255,255)->generate('RET-' . $borrowing->transaction_code) !!}
                    </div>
                    <div class="mt-4" style="font-family:'Fredoka One',cursive; font-size:0.85rem; color:#888; letter-spacing:2px;">
                        RET-{{ $borrowing->transaction_code }}
                    </div>
                    <div class="mt-2" style="font-weight:700; color:var(--comic-red);">
                        ⚠️ Tunjukkan QR ini ke admin untuk verifikasi pengembalian
                    </div>
                    <div class="mt-2" style="font-weight:700; color:#888; font-size:0.85rem;">
                        Admin akan scan QR ini di sistem untuk memproses pengembalian buku Anda.
                    </div>
                </div>
            </div>

            {{-- Back Button --}}
            <div class="text-center mt-4">
                <a href="{{ route('member.borrowings', ['code' => $member->member_code]) }}"
                   class="btn fw-bold px-5"
                   style="background:var(--comic-dark); color:#fff; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-orange); border-radius:0; font-family:'Bangers',cursive; font-size:1.1rem; letter-spacing:2px;">
                    ← KEMBALI KE RIWAYAT
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
<footer class="comic-footer py-4 mt-5">
    <div class="container">
        <div class="text-center text-light">
            <div style="font-family:'Fredoka One',cursive; color:var(--comic-orange); letter-spacing:2px;">
                📚 {{ app_setting('app_name', 'Perpustakaan Modern') }}
            </div>
        </div>
    </div>
</footer>
@endsection