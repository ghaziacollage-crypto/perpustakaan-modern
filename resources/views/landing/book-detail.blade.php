@extends('landing.layout')

@section('title', $book->title . ' — Detail Buku')
@section('page-title', 'Detail Buku')

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-icon">📚</span>
            <span class="brand-text fw-black">{{ app_setting('app_name', 'Aplikasi Perpustakaan') }}</span>
        </a>
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navDetail">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navDetail">
            <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="/">🏠 Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('landing.books') }}">📖 Koleksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('landing.categories') }}">🗂️ Kategori</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('member.index') ? 'active-link' : '' }} btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.index') }}">👤 Member</a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="btn btn-warning btn-sm px-3 fw-bold text-dark" href="{{ route('admin.dashboard') }}">📊 Dashboard</a>
                </li>
                @else
                <li class="nav-item">
                    <a class="btn btn-warning btn-sm px-3 fw-bold text-dark" href="{{ route('login') }}">🔑 Login</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="--bs-breadcrumb-divider-color: rgba(255,255,255,0.4);">
                <li class="breadcrumb-item"><a href="{{ route('landing.books') }}" class="text-orange fw-bold text-decoration-none">📖 Koleksi</a></li>
                <li class="breadcrumb-item text-white-50">{{ $book->category->name ?? '-' }}</li>
                <li class="breadcrumb-item text-white" aria-current="page">{{ Str::limit($book->title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5" style="background: var(--comic-cream);">
    <div class="container">
        <div class="book-detail-card mx-auto" style="max-width: 900px;">
            <div class="row g-4 align-items-start">
                <div class="col-md-4 text-center">
                    @if($book->cover)
                    <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" class="detail-cover img-fluid"/>
                    @else
                    <div class="detail-cover img-fluid d-flex align-items-center justify-content-center" style="height: 350px; background: var(--comic-orange);">
                        <span style="font-family: 'Bangers', cursive; font-size: 5rem; color: #fff;">{{ Str::substr($book->title, 0, 1) }}</span>
                    </div>
                    @endif

                    <div class="mt-4 p-3" style="border: 2px solid var(--comic-dark); box-shadow: 3px 3px 0 var(--comic-dark);">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div style="font-size:1.6rem;">📦</div>
                                <div class="fw-black" style="font-size:1.1rem;">{{ $book->stock }}</div>
                                <div class="text-muted" style="font-size:0.7rem;letter-spacing:1px;">STOK</div>
                            </div>
                            <div class="col-4 border-start border-dark">
                                <div style="font-size:1.6rem;">📍</div>
                                <div class="fw-black" style="font-size:1.1rem;">{{ $book->rack_location }}</div>
                                <div class="text-muted" style="font-size:0.7rem;letter-spacing:1px;">RAK</div>
                            </div>
                            <div class="col-4 border-start border-dark">
                                <div style="font-size:1.6rem;">🏷️</div>
                                <div class="fw-black" style="font-size:1.1rem;">{{ $book->category->name ?? '-' }}</div>
                                <div class="text-muted" style="font-size:0.7rem;letter-spacing:1px;">KATEGORI</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-2">
                        <span class="badge" style="background: var(--comic-orange); border: 2px solid var(--comic-dark); color: #fff; font-weight: 900; padding: 5px 12px;">
                            {{ $book->category->name ?? 'Tanpa Kategori' }}
                        </span>
                        @if($book->stock > 0)
                        <span class="badge bg-success ms-2" style="border: 2px solid var(--comic-dark); font-weight: 900;">TERSEDIA</span>
                        @else
                        <span class="badge" style="background: var(--comic-red); border: 2px solid var(--comic-dark); color: #fff; font-weight: 900;">HABIS</span>
                        @endif
                    </div>

                    <h1 class="fw-black mb-2" style="font-family: 'Fredoka One', cursive; font-size: 2.2rem; line-height: 1.2;">{{ $book->title }}</h1>
                    <p class="text-muted fw-bold mb-3" style="font-size: 1.1rem;">oleh {{ $book->author }}</p>

                    <div class="info-grid mb-4">
                        <div class="info-item">
                            <span class="info-label">📖 ISBN</span>
                            <span class="info-value">{{ $book->isbn ?: '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">📝 PENERBIT</span>
                            <span class="info-value">{{ $book->publisher ?: '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">📅 TAHUN</span>
                            <span class="info-value">{{ $book->published_year ?: '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">📦 HALAMAN</span>
                            <span class="info-value">{{ $book->pages ?: '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">📍 LOKASI RAK</span>
                            <span class="info-value">{{ $book->rack_location }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">🔢 STOCK</span>
                            <span class="info-value">{{ $book->stock }} eksemplar</span>
                        </div>
                    </div>

                    <div class="synopsis-box">
                        <h6 class="fw-black mb-2">📝 SINOPSIS</h6>
                        <p style="line-height: 1.8;">{!! nl2br(e($book->synopsis ?: 'Sinposis tidak tersedia untuk buku ini.')) !!}</p>
                    </div>

                    <div class="d-flex gap-3 mt-4 flex-wrap">
                        <a href="{{ route('landing.books') }}" class="btn btn-dark fw-black px-4" style="border-radius:0; border:3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark);">
                            ← KEMBALI
                        </a>
                        @auth
                        <a href="{{ route('admin.borrowings.index') }}" class="btn btn-orange fw-black px-4" style="border-radius:0; border:3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark);">
                            PINJAM BUKU INI →
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-orange fw-black px-4" style="border-radius:0; border:3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark);">
                            🔑 LOGIN UNTUK PINJAM
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection