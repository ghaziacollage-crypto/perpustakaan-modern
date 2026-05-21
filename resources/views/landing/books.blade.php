@extends('landing.layout')

@section('title', 'Koleksi Buku')
@section('page-title', 'Koleksi Buku')

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-icon">📚</span>
            <span class="brand-text fw-black">{{ app_setting('app_name', 'Aplikasi Perpustakaan') }}</span>
        </a>
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navBooks">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navBooks">
            <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="/">🏠 Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active-link btn btn-dark btn-sm px-3 fw-bold" href="{{ route('landing.books') }}">📖 Koleksi</a>
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

<div class="page-hero">
    <div class="container position-relative">
        <div class="text-center text-white">
            <div class="section-label" style="color: var(--comic-yellow);">EXPLORE</div>
            <h1 class="comic-section-title text-white mb-2">📚 KOLEKSI <span class="text-orange">BUKU</span></h1>
            <p class="text-white-50 fw-bold">Telusuri seluruh koleksi bukuperpustakaan kami</p>
        </div>
    </div>
</div>

<section class="py-5" style="background: var(--comic-cream); min-height: 70vh;" id="books">
    <div class="container">
        {{-- Search & Filter Bar --}}
        <div class="comic-search-bar mb-5">
            <form method="GET" action="{{ route('landing.books') }}" class="row g-3 align-items-end" id="searchForm">
                <div class="col-md-6">
                    <label class="form-label fw-black text-light">🔍 Pencarian</label>
                    <input type="text" name="search" id="searchInput" class="form-control form-control-lg fw-bold"
                        placeholder="Cari judul atau penulis..." value="{{ $search }}"/>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-black text-light">🗂️ Kategori</label>
                    <select name="category" id="categorySelect" class="form-select form-select-lg fw-bold">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-orange btn-lg w-100 fw-black shadow-comic">CARI</button>
                </div>
            </form>
        </div>

        {{-- Results Header --}}
        @if($search || request('category'))
        <div class="mb-4 text-center">
            <span class="badge" style="background: var(--comic-dark); color: var(--comic-yellow); font-weight: 900; padding: 8px 16px; font-size: 0.9rem;">
                Hasil pencarian{{ $search ? ' untuk "' . $search . '"' : '' }}{{ request('category') ? ' di kategori ' . ($categories->find(request('category'))->name ?? '') : '' }}
            </span>
        </div>
        @endif

        {{-- Books Grid --}}
        @if($books->count())
        <div class="row g-4" id="booksGrid">
            @foreach($books as $book)
            <div class="col-6 col-md-4 col-lg-3">
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
                        @if($book->stock <= 3 && $book->stock > 0)
                        <div class="stock-warning-badge">TERSISA {{ $book->stock }}!</div>
                        @elseif($book->stock == 0)
                        <div class="stock-empty-badge">STOK HABIS</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-tag">{{ $book->category->name ?? '-' }}</div>
                        <h5 class="card-title fw-black">{{ Str::limit($book->title, 35) }}</h5>
                        <p class="card-author">{{ $book->author }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stock-badge {{ $book->stock == 0 ? 'stock-empty' : '' }}">📦 {{ $book->stock > 0 ? $book->stock . ' tersedia' : 'Habis' }}</div>
                            <div class="rack-location">📍 {{ $book->rack_location }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5" id="paginationWrapper">
            @include('landing.partials.pagination', ['paginator' => $books, 'ariaLabel' => 'Navigasi halaman koleksi buku'])
        </div>
        @else
        <div class="empty-comic-box text-center">
            <div class="empty-icon">📭</div>
            <h3 class="fw-black">BUKU TIDAK DITEMUKAN</h3>
            <p class="fw-bold text-muted">Coba kata kunci lain atau pilih kategori berbeda.</p>
            <a href="{{ route('landing.books') }}" class="btn btn-orange fw-black px-4">RESET PENCARIAN</a>
        </div>
        @endif
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
@endsection

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Book modal ──
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

    // ── Scroll to books section after pagination click ──
    var booksSection = document.getElementById('books');
    var paginationWrapper = document.getElementById('paginationWrapper');
    if (paginationWrapper && booksSection) {
        paginationWrapper.addEventListener('click', function(e) {
            var link = e.target.closest('a.page-btn');
            if (link) {
                e.preventDefault();
                var url = link.getAttribute('href');
                // Smooth scroll to books section BEFORE navigation
                booksSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Navigate after a brief delay so scroll takes effect
                setTimeout(function() {
                    window.location.href = url;
                }, 300);
            }
        });
    }
});
</script>
@endpush