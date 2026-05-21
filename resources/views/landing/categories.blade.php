@extends('landing.layout')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-icon">📚</span>
            <span class="brand-text fw-black">{{ app_setting('app_name', 'Aplikasi Perpustakaan') }}</span>
        </a>
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navCats">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navCats">
            <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="/">🏠 Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('landing.books') }}">📖 Koleksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active-link btn btn-dark btn-sm px-3 fw-bold" href="{{ route('landing.categories') }}">🗂️ Kategori</a>
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

<div class="page-hero">
    <div class="container position-relative">
        <div class="text-center text-white">
            <div class="section-label" style="color: var(--comic-yellow);">BROWSE</div>
            <h1 class="comic-section-title text-white mb-2">📚 KATEGORI BUKU</h1>
            <p class="text-white-50 fw-bold">Pilih kategori untuk melihat koleksi buku yang tersedia</p>
        </div>
    </div>
</div>

<section class="py-5" style="background: var(--comic-cream); min-height: 70vh;">
    <div class="container">
        @if($categories->count())
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('landing.books', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="category-page-card">
                        <span class="cat-icon">{!! $category->icon ?? '📚' !!}</span>
                        <div class="cat-name">{{ $category->name }}</div>
                        <div class="cat-count">{{ $category->books_count }} buku tersedia</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-comic-box text-center">
            <div class="empty-icon">📂</div>
            <h3 class="fw-black">BELUM ADA KATEGORI</h3>
            <p class="text-muted fw-bold">Admin belum menambahkan kategori buku.</p>
        </div>
        @endif
    </div>
</section>
@endsection