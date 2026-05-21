@extends('landing.layout')

@section('title', 'Beranda — ' . app_setting('app_name', 'Aplikasi Perpustakaan'))
@section('page-title', 'Beranda')

@section('content')
{{-- HERO SLIDER SECTION --}}
<section class="hero-slider-section position-relative overflow-hidden" id="heroSlider">
    {{-- Sticky Floating Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 py-lg-3 sticky-top">
        <div class="container position-relative">
            <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
                <span class="brand-icon">📚</span>
                <span class="brand-text fw-black">{{ app_setting('app_name', 'Aplikasi Perpustakaan') }}</span>
            </a>
            <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navMenuSlider">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenuSlider">
                <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('landing.home') || request()->routeIs('landing.books') && !request('category') && !request('search') ? 'active-link' : '' }} btn btn-dark btn-sm px-3 fw-bold" href="/">🏠 Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('landing.books') ? 'active-link' : '' }} btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('landing.books') }}">📖 Koleksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('landing.categories') ? 'active-link' : '' }} btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('landing.categories') }}">🗂️ Kategori</a>
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

    @if($slides->count())
    <div id="comicHeroCarousel" class="carousel slide comic-carousel" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            @foreach($slides as $i => $slide)
            <button type="button" data-bs-target="#comicHeroCarousel" data-bs-slide-to="{{ $i }}"
                class="{{ $i === 0 ? 'active' : '' }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}"></button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @foreach($slides as $i => $slide)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}" data-bs-interval="5000">
                <div class="slide-bg" style="background-image: url('{{ asset('storage/' . $slide->image_url) }}');">
                    <div class="slide-overlay"></div>
                </div>
                <div class="container">
                    <div class="row align-items-center min-vh-600">
                        <div class="col-lg-7">
                            <div class="slide-content" data-aos="fade-up">
                                <div class="slide-tag">
                                    <span>{{ $i === 0 ? '✨ SELAMAT DATANG' : '📚 KOLEKSI BARU' }}</span>
                                </div>
                                <h1 class="slide-title">{{ $slide->title }}</h1>
                                @if($slide->subtitle)
                                <p class="slide-subtitle">{{ $slide->subtitle }}</p>
                                @endif
                                <div class="slide-actions">
                                    @if($slide->link_url)
                                    <a href="{{ $slide->link_url }}" class="btn btn-orange-slide fw-black px-4">
                                        {{ $slide->link_text ?: 'Lihat Selengkapnya' }} →
                                    </a>
                                    @endif
                                    <a href="{{ route('landing.books') }}" class="btn btn-outline-light-slide fw-black px-4">
                                        📖 Lihat Koleksi
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center">
                            <div class="slide-illustration">
                                @if($slide->illustration_type === 'image' && $slide->illustration_image)
                                    <div class="slide-illustration-image">
                                        <img src="{{ asset('storage/' . $slide->illustration_image) }}" alt="Ilustrasi" class="ill-img"/>
                                        <div class="ill-glow"></div>
                                    </div>
                                @else
                                    <div class="floating-books">
                                        <div class="float-book fb-1">📕</div>
                                        <div class="float-book fb-2">📗</div>
                                        <div class="float-book fb-3">📘</div>
                                        <div class="float-book fb-4">📙</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#comicHeroCarousel" data-bs-slide="prev">
            <span class="carousel-arrow">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#comicHeroCarousel" data-bs-slide="next">
            <span class="carousel-arrow">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </button>
    </div>
    @else
    {{-- Fallback: static hero when no slides --}}
    <div class="hero-fallback">
        <nav class="navbar navbar-expand-lg navbar-dark comic-navbar py-3">
            <div class="container">
                <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
                    <span class="brand-icon">📚</span>
                    <span class="brand-text fw-black">{{ app_setting('app_name', 'Aplikasi Perpustakaan') }}</span>
                </a>
                <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav ms-auto gap-2">
                        <li class="nav-item"><a class="nav-link active-link btn btn-dark btn-sm px-3 fw-bold" href="/">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-outline-dark btn-sm px-3 fw-bold" href="{{ route('landing.books') }}">Koleksi</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-outline-dark btn-sm px-3 fw-bold" href="{{ route('landing.categories') }}">Kategori</a></li>
                        @auth
                        <li class="nav-item"><a class="btn btn-warning btn-sm px-3 fw-bold text-dark" href="{{ route('admin.dashboard') }}">📊 Dashboard</a></li>
                        @else
                        <li class="nav-item"><a class="btn btn-warning btn-sm px-3 fw-bold text-dark" href="{{ route('login') }}">🔑 Login</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container py-5">
            <div class="row align-items-center min-vh-600">
                <div class="col-lg-7">
                    <div class="speech-bubble speech-dark mb-4">
                        <h1 class="comic-title display-3 fw-black text-dark mb-2">EXPLORE<br><span class="text-orange">YOUR</span><br>READING<br>WORLD! <span class="boom-text">💥</span></h1>
                        <p class="lead fw-bold text-dark">Rak buku digital interaktif dengan nuansa perpustakaan komik modern. Temukan, pinjam, dan nikmati bacaan favoritmu!</p>
                    </div>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('landing.books') }}" class="btn btn-orange btn-lg fw-black px-4 shadow-comic">📖 LIHAT KOLEKSI</a>
                        <a href="{{ route('landing.categories') }}" class="btn btn-dark btn-lg fw-black px-4 shadow-comic">🗂️ KATEGORI</a>
                    </div>
                    <div class="mt-4 d-flex gap-4 stat-badges">
                        <div class="stat-item"><span class="stat-num">{{ number_format($totalBooks) }}</span><span class="stat-label">Buku</span></div>
                        <div class="stat-divider">|</div>
                        <div class="stat-item"><span class="stat-num">{{ number_format($totalCategories) }}</span><span class="stat-label">Kategori</span></div>
                        <div class="stat-divider">|</div>
                        <div class="stat-item"><span class="stat-num">24/7</span><span class="stat-label">Online</span></div>
                    </div>
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0">
                    <div class="hero-comic-panel">
                        <div class="panel-inner">
                            <div class="book-stack">
                                <div class="stacked-book book-1">📕</div>
                                <div class="stacked-book book-2">📗</div>
                                <div class="stacked-book book-3">📘</div>
                                <div class="stacked-book book-4">📙</div>
                                <div class="stacked-book book-5">📓</div>
                            </div>
                            <div class="pop-effect" id="heroPop">POW!</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

{{-- STATS BANNER --}}
<div class="stats-banner py-3">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-4 col-md-4">
                <div class="stat-box">
                    <span class="stat-number anim-pop" style="display:inline-block; animation-delay:0.1s;">{{ number_format($totalBooks) }}</span>
                    <span class="stat-text">Total Buku</span>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-box">
                    <span class="stat-number anim-pop" style="display:inline-block; animation-delay:0.2s;">{{ number_format($totalCategories) }}</span>
                    <span class="stat-text">Kategori</span>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-box">
                    <span class="stat-number anim-pop" style="display:inline-block; animation-delay:0.3s;">24/7</span>
                    <span class="stat-text">Akses Online</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CATEGORY BUBBLES --}}
@if($categories->count())
<section class="category-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label anim-bounce">BROWSE BY</div>
            <h2 class="comic-section-title">KATEGORI <span class="text-orange">BUKU</span></h2>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach($categories->take(8) as $index => $category)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('landing.books', ['category' => $category->id]) }}" class="category-bubble text-decoration-none d-block text-center anim-slide-up" style="animation-delay: {{ ($index % 4) * 0.12 }}s;">
                    <div class="bubble-icon">{!! $category->icon ?? '📚' !!}</div>
                    <div class="bubble-name fw-black">{{ $category->name }}</div>
                    <div class="bubble-count">{{ $category->books_count }} buku</div>
                </a>
            </div>
            @endforeach
        </div>
        @if($categories->count() > 8)
        <div class="text-center mt-4">
            <a href="{{ route('landing.categories') }}" class="btn btn-dark btn-lg fw-black px-5 shadow-comic">LIHAT SEMUA KATEGORI →</a>
        </div>
        @endif
    </div>
</section>
@endif

{{-- POPULAR BOOKS CAROUSEL --}}
@if($popularBooks->count())
<section class="popular-section py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <div class="section-label anim-bounce" style="animation-delay:0.1s;">HOT!</div>
                <h2 class="comic-section-title m-0" style="color:#fff;">BUKU <span class="text-orange">POPULER</span> ⭐</h2>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-dark btn-sm fw-bold" id="prevPopular">◀ PREV</button>
                <button class="btn btn-orange btn-sm fw-bold" id="nextPopular">NEXT ▶</button>
            </div>
        </div>
        <div class="comic-carousel-list" id="popularCarousel">
            <div class="carousel-track" id="popularTrack">
                @foreach($popularBooks as $index => $book)
                <div class="comic-card-wrapper anim-slide-up" style="animation-delay: {{ ($index % 4) * 0.1 }}s;">
                    <div class="comic-card book-card" data-book-id="{{ $book->id }}"
                        data-title="{{ $book->title }}"
                        data-author="{{ $book->author }}"
                        data-category="{{ $book->category->name ?? '-' }}"
                        data-stock="{{ $book->stock }}"
                        data-isbn="{{ $book->isbn ?? '-' }}"
                        data-rack="{{ $book->rack_location }}"
                        data-synopsis="{{ Str::limit(strip_tags($book->synopsis ?? 'Tidak ada sinopsis tersedia.'), 200) }}"
                        data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : 'https://placehold.co/200x280/FF6B35/fff?text=' . urlencode(Str::substr($book->title, 0, 1)) }}">
                        <div class="card-comic-border">
                            @if($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" class="card-img-top comic-book-cover"/>
                            @else
                            <div class="comic-no-cover">{{ Str::substr($book->title, 0, 1) }}</div>
                            @endif
                            <div class="pop-overlay">LIHAT!</div>
                        </div>
                        <div class="card-body">
                            <div class="card-tag">{{ $book->category->name ?? '-' }}</div>
                            <h5 class="card-title fw-black">{{ Str::limit($book->title, 30) }}</h5>
                            <p class="card-author">{{ $book->author }}</p>
                            <div class="stock-badge">📦 {{ $book->stock }} tersedia</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- SEARCH CTA SECTION --}}
<section class="search-cta-section py-5">
    <div class="container">
        <div class="search-cta-card anim-slide-up" style="animation-delay:0.1s;">
            <span class="scta-deco scta-left">📖</span>
            <span class="scta-deco scta-right">📚</span>
            <div class="scta-label">TEMUKAN BUKU</div>
            <h2 class="scta-title">CARI KOLEKSI <span class="text-orange">FAVORIT</span>MU!</h2>
            <p class="scta-desc">Ribuan buku menunggumu! Cari berdasarkan judul, penulis, atau kategori kesukaanmu.</p>
            <form action="{{ route('landing.books') }}" method="GET" class="scta-search-form">
                <div class="scta-inputs">
                    <div class="scta-input-wrap">
                        <span class="scta-icon">🔍</span>
                        <input type="text" name="search" class="scta-input" placeholder="Cari judul, penulis..." value="{{ request('search') }}">
                    </div>
                    <div class="scta-input-wrap" style="flex: 0 0 auto;">
                        <select name="category" class="scta-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-orange scta-btn">CARI BUKU!</button>
                </div>
                <p class="scta-hint">Tekan ENTER atau klik tombol untuk mencari</p>
            </form>
        </div>
    </div>
</section>

{{-- BOOK DETAIL MODAL --}}
<div class="modal fade" id="bookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content comic-modal" id="bookModalContent">
            <div class="modal-header border-dark">
                <h4 class="modal-title fw-black" id="bookModalTitle">DETAIL BUKU</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookModalBody">
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <img id="modalCover" src="" alt="" class="img-fluid rounded-4 shadow-comic"/>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-2">
                            <span class="badge bg-orange text-dark fw-bold" id="modalCategory">-</span>
                        </div>
                        <h3 class="fw-black mb-1" id="modalTitle">-</h3>
                        <p class="text-muted fw-bold mb-3" id="modalAuthor">-</p>
                        <div class="info-grid mb-3">
                            <div class="info-item"><span class="info-label">📦 Stok</span><span class="info-value" id="modalStock">-</span></div>
                            <div class="info-item"><span class="info-label">📖 ISBN</span><span class="info-value" id="modalIsbn">-</span></div>
                            <div class="info-item"><span class="info-label">📍 Rak</span><span class="info-value" id="modalRack">-</span></div>
                        </div>
                        <div class="synopsis-box">
                            <h6 class="fw-black mb-2">📝 SINOPSIS</h6>
                            <p class="mb-0" id="modalSynopsis">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-dark">
                <button type="button" class="btn btn-dark fw-black" data-bs-dismiss="modal">TUTUP</button>
                @auth
                <a href="{{ route('admin.borrowings.index') }}" class="btn btn-orange fw-black">PINJAM BUKU →</a>
                @else
                <a href="{{ route('login') }}" class="btn btn-orange fw-black">🔑 LOGIN UNTUK PINJAM</a>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer class="comic-footer py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-black text-orange mb-3">📚 {{ app_setting('app_name', 'Aplikasi Perpustakaan') }}</h5>
                <p class="text-light mb-0">Sistem manajemen perpustakaan digital modern dengan nuansa komik interaktif.</p>
            </div>
            <div class="col-md-4">
                <h6 class="fw-black text-orange mb-3">QUICK LINKS</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('landing.books') }}" class="text-light text-decoration-none fw-bold">📖 Koleksi Buku</a></li>
                    <li><a href="{{ route('landing.categories') }}" class="text-light text-decoration-none fw-bold">🗂️ Kategori</a></li>
                    <li><a href="{{ route('login') }}" class="text-light text-decoration-none fw-bold">🔑 Login Admin</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-black text-orange mb-3">INFO</h6>
                <p class="text-light mb-1">📍 Rak Buku: A1 - Z99</p>
                <p class="text-light mb-1">⏰ Buka: 07.00 - 16.00 WIB</p>
                <p class="text-light mb-0">📞 Hubungi admin untuk bantuan</p>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="text-center text-light mb-0 fw-bold">&copy; {{ date('Y') }} {{ app_setting('app_name', 'Aplikasi Perpustakaan') }}. All rights reserved.</p>
    </div>
</footer>
@endsection

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Book modal
    document.querySelectorAll('.comic-card').forEach(function(card) {
        card.addEventListener('click', function() {
            document.getElementById('modalTitle').textContent = card.dataset.title;
            document.getElementById('modalAuthor').textContent = card.dataset.author;
            document.getElementById('modalCategory').textContent = card.dataset.category;
            document.getElementById('modalStock').textContent = card.dataset.stock + ' exemplar';
            document.getElementById('modalIsbn').textContent = card.dataset.isbn;
            document.getElementById('modalRack').textContent = card.dataset.rack;
            document.getElementById('modalSynopsis').textContent = card.dataset.synopsis;
            document.getElementById('modalCover').src = card.dataset.cover;
            document.getElementById('modalCover').alt = card.dataset.title;
            new bootstrap.Modal(document.getElementById('bookModal')).show();
        });
    });

    // Popular carousel
    var track = document.getElementById('popularTrack');
    if (track) {
        document.getElementById('prevPopular')?.addEventListener('click', function() { track.scrollBy({ left: -300, behavior: 'smooth' }); });
        document.getElementById('nextPopular')?.addEventListener('click', function() { track.scrollBy({ left: 300, behavior: 'smooth' }); });
    }

    // Hero pop
    var heroPop = document.getElementById('heroPop');
    if (heroPop) { setTimeout(function() { heroPop.style.opacity = '1'; heroPop.style.transform = 'translate(-50%,-50%) scale(1.2)'; }, 800); setTimeout(function() { heroPop.style.opacity = '0'; heroPop.style.transform = 'translate(-50%,-50%) scale(0)'; }, 2000); }
});
</script>
@endpush