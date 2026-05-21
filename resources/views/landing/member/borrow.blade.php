@extends('landing.layout')

@section('title', 'Pinjam Buku — ' . $member->name)

@push('custom-css')
<style>
    .book-card-member {
        position: relative;
        background: #fff;
        border: 3px solid var(--comic-dark);
        box-shadow: 4px 4px 0 var(--comic-dark);
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s;
    }
    .book-card-member:hover { transform: translateY(-4px); box-shadow: 6px 8px 0 var(--comic-dark); }
    .book-card-member.selected { border-color: var(--comic-orange); box-shadow: 4px 4px 0 var(--comic-orange); }
    .book-card-member.out-of-stock { opacity: 0.6; cursor: not-allowed; }
    .book-checkbox {
        position: absolute; top:8px; left:8px; z-index:10;
        width:28px; height:28px; border-radius:0;
        border:3px solid var(--comic-dark);
        background:#fff; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        font-size:1.1rem;
    }
    .book-card-member.selected .book-checkbox {
        background:var(--comic-orange); color:#fff;
    }
    .book-card-member.out-of-stock .book-checkbox { background:#eee; cursor:not-allowed; }
    .slot-warning {
        background:#fff0f0; border:3px solid var(--comic-red);
        box-shadow:4px 4px 0 var(--comic-red); padding:15px;
        font-family:'Fredoka One',cursive; font-size:0.9rem;
    }
</style>
@endpush

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-icon">📚</span>
            <span class="brand-text fw-black">{{ app_setting('app_name', 'Perpustakaan') }}</span>
        </a>
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navBorrow">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navBorrow">
            <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.dashboard', ['code' => $member->member_code]) }}">🏠 Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-dark btn-sm px-3 fw-bold" href="{{ route('member.books', ['code' => $member->member_code]) }}">📖 Pinjam Buku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.borrowings', ['code' => $member->member_code]) }}">📋 Riwayat</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Header --}}
<div class="detail-hero" style="min-height:20vh;">
    <div class="container">
        <div class="text-center text-white">
            <div class="section-label" style="color:var(--comic-yellow);">PINJAM BUKU</div>
            <h1 class="comic-section-title text-white mb-2">📖 PILIH BUKU YANG INGIN <span class="text-orange">DIPINJAM</span></h1>
            <p class="text-white-50 fw-bold">Pilih buku dengan centang, lalu konfirmasi di bawah</p>
        </div>
    </div>
</div>

{{-- Slot Warning --}}
@if($member->remaining_slots == 0)
<div class="container py-3">
    <div class="slot-warning text-center">
        ⚠️ SLOT PEMINJAMAN HABIS! Kembalikan buku terlebih dahulu untuk bisa pinjam lagi.
    </div>
</div>
@endif

{{-- Main Content --}}
<div class="container py-5">
    {{-- Search --}}
    <div class="comic-search-bar mb-4">
        <form method="GET" action="{{ route('member.books', ['code' => $member->member_code]) }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-bold text-light">🔍 Pencarian</label>
                <input type="text" name="search" class="form-control form-control-lg fw-bold"
                    placeholder="Judul atau penulis..." value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-light">🗂️ Kategori</label>
                <select name="category" class="form-select form-select-lg fw-bold">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-orange btn-lg w-100 fw-bold" style="border-radius:0; border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark);">🔍 CARI</button>
            </div>
        </form>
    </div>

    {{-- Selected Count + Submit Button --}}
    <div id="selectionBar" class="d-none mb-4" style="position:sticky; top:70px; z-index:20;">
        <div style="background:var(--comic-dark); border:4px solid var(--comic-orange); box-shadow:5px 5px 0 var(--comic-orange); padding:15px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:var(--comic-orange);">
                ✅ <span id="selectedCount">0</span> buku dipilih
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn fw-bold" onclick="clearSelection()"
                    style="background:#fff; color:var(--comic-dark); border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); border-radius:0;">
                    🔄 Reset
                </button>
                <button type="button" class="btn fw-bold" onclick="showConfirmModal()"
                    style="background:var(--comic-orange); color:#fff; border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); border-radius:0; font-size:1rem; padding:10px 24px;">
                    📚 PINJAM <span id="selectedCountBtn">0</span> BUKU
                </button>
            </div>
        </div>
    </div>

    {{-- Books Grid --}}
    <div class="row g-3">
        @forelse($books as $book)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="book-card-member {{ $book->stock == 0 ? 'out-of-stock' : '' }}"
                 data-book-id="{{ $book->id }}"
                 data-title="{{ $book->title }}"
                 data-author="{{ $book->author }}"
                 data-stock="{{ $book->stock }}"
                 data-isbn="{{ $book->isbn ?? '-' }}"
                 data-category="{{ $book->category->name ?? '-' }}"
                 data-description="{{ Str::limit(strip_tags($book->description ?? 'Tidak ada deskripsi.'), 200) }}"
                 data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}"
                 data-initial="{{ substr($book->title, 0, 1) }}"
                 onclick="openBookDetail(this)">
                <div class="book-checkbox">
                    <span class="check-icon">✓</span>
                </div>
                <div class="card-comic-border">
                    @if($book->cover)
                        <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" class="card-img-top" style="height:180px; object-fit:cover;">
                    @else
                        <div style="height:180px; background:var(--comic-orange); display:flex; align-items:center; justify-content:center;">
                            <span style="font-family:'Bangers',cursive; font-size:3rem; color:#fff;">{{ substr($book->title,0,1) }}</span>
                        </div>
                    @endif
                    @if($book->stock == 0)
                        <div style="position:absolute; inset:0; background:rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center;">
                            <span style="background:var(--comic-red); color:#fff; font-family:'Bangers',cursive; font-size:1rem; padding:5px 15px; border:2px solid #fff;">STOK HABIS</span>
                        </div>
                    @elseif($book->stock <= 3)
                        <div style="position:absolute; top:8px; right:8px; background:var(--comic-yellow); border:2px solid var(--comic-dark); font-family:'Fredoka One',cursive; font-size:0.65rem; padding:2px 8px;">TERSISA {{ $book->stock }}</div>
                    @endif
                </div>
                <div class="card-body" style="padding:12px;">
                    <div style="font-size:0.7rem; color:#888; font-weight:700; margin-bottom:4px;">{{ $book->category->name ?? '-' }}</div>
                    <div style="font-family:'Fredoka One',cursive; font-size:0.85rem; color:var(--comic-dark); line-height:1.3; margin-bottom:4px;">{{ Str::limit($book->title, 40) }}</div>
                    <div style="font-size:0.75rem; color:#aaa; font-weight:700;">{{ $book->author }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-comic-box text-center">
                <div class="empty-icon">📭</div>
                <h3 class="fw-black">BUKU TIDAK DITEMUKAN</h3>
                <p class="fw-bold text-muted">Coba kata kunci atau kategori lain.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($books->hasPages())
    <div class="d-flex justify-content-center mt-5">
        @include('landing.partials.pagination', [
            'paginator' => $books,
            'ariaLabel' => 'Navigasi halaman buku'
        ])
    </div>
    @endif
</div>

{{-- Book Detail Modal --}}
<div class="modal fade" id="bookDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content comic-modal" style="border:5px solid var(--comic-dark);">
            <div class="modal-header" style="background:var(--comic-dark); border-bottom:4px solid var(--comic-orange);">
                <h5 class="modal-title" style="font-family:'Bangers',cursive; color:var(--comic-orange); letter-spacing:2px; font-size:1.4rem;">📖 DETAIL BUKU</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body" style="background:var(--comic-cream); padding:30px;">
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <div id="modalBookCover" style="width:100%; max-width:180px; aspect-ratio:2/3; background:var(--comic-orange); margin:0 auto; display:flex; align-items:center; justify-content:center; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark);">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h3 id="modalBookTitle" style="font-family:'Bangers',cursive; color:var(--comic-dark); font-size:1.5rem; margin-bottom:8px;"></h3>
                        <p id="modalBookAuthor" style="font-size:1rem; color:#666; font-weight:700; margin-bottom:15px;"></p>
                        <div class="mb-3">
                            <span id="modalBookCategory" class="badge" style="background:var(--comic-blue); color:#fff; font-size:0.8rem; padding:6px 14px; border:2px solid var(--comic-dark);"></span>
                        </div>
                        <div style="background:#fff; border:3px solid var(--comic-dark); padding:15px; margin-bottom:15px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-weight:700;">📦 Stok:</span>
                                <span id="modalBookStock" style="font-family:'Fredoka One',cursive; color:var(--comic-dark); font-size:1.1rem;"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-weight:700;">📚 ISBN:</span>
                                <span id="modalBookIsbn" style="font-size:0.85rem; color:#555;"></span>
                            </div>
                        </div>
                        <div style="background:var(--comic-yellow); border:3px solid var(--comic-dark); padding:12px; font-weight:700; font-size:0.9rem;">
                            <span id="modalBookDesc"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:var(--comic-dark); border-top:3px solid var(--comic-orange);">
                <button type="button" class="btn" data-bs-dismiss="modal"
                    style="background:#fff; color:var(--comic-dark); border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); border-radius:0; font-weight:700;">
                    Tutup
                </button>
                <button type="button" class="btn fw-bold" id="btnAddToBorrow"
                    style="background:var(--comic-orange); color:#fff; border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); border-radius:0; font-size:1rem;">
                    ➕ Tambahkan ke Peminjaman
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content comic-modal">
            <div class="modal-header" style="background:var(--comic-dark); border-bottom:4px solid var(--comic-orange);">
                <h5 class="modal-title" style="font-family:'Bangers',cursive; color:var(--comic-orange); letter-spacing:2px; font-size:1.3rem;">📚 KONFIRMASI PEMINJAMAN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body" style="background:var(--comic-cream);">
                <div style="font-weight:700; margin-bottom:12px;">Buku yang dipilih:</div>
                <div id="confirmBookList" style="max-height:200px; overflow-y:auto; margin-bottom:15px;"></div>
                <div style="background:#fff; border:3px solid var(--comic-dark); padding:12px; font-weight:700;">
                    📅 Jatuh tempo: <span style="color:var(--comic-orange);">{{ now()->addDays(7)->format('d M Y') }}</span>
                    <br>
                    📦 Sisa slot Anda: <span style="color:var(--comic-blue);">{{ $member->remaining_slots }} buku</span>
                </div>
                <div id="confirmError" class="d-none mt-3" style="background:#fff0f0; border:3px solid var(--comic-red); padding:10px; font-weight:700; color:var(--comic-red);"></div>
            </div>
            <div class="modal-footer" style="background:var(--comic-dark); border-top:3px solid var(--comic-orange);">
                <button type="button" class="btn fw-bold" data-bs-dismiss="modal"
                    style="background:#fff; color:var(--comic-dark); border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); border-radius:0;">
                    Batal
                </button>
                <button type="button" class="btn fw-bold" id="btnConfirmBorrow"
                    style="background:var(--comic-orange); color:#fff; border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); border-radius:0;">
                    ✅ Konfirmasi Pinjam
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Success Modal --}}
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content comic-modal">
            <div class="modal-header" style="background:#27ae60; border-bottom:4px solid var(--comic-dark);">
                <h5 class="modal-title" style="font-family:'Bangers',cursive; color:#fff; letter-spacing:2px; font-size:1.3rem;">✅ BERHASIL!</h5>
            </div>
            <div class="modal-body text-center" style="background:var(--comic-cream); padding:40px;">
                <div style="font-size:4rem; margin-bottom:15px;">🎉</div>
                <div style="font-family:'Bangers',cursive; font-size:1.5rem; color:var(--comic-dark); letter-spacing:2px; margin-bottom:10px;">PEMINJAMAN DIAJUKAN!</div>
                <div style="font-weight:700; color:#888; margin-bottom:5px;">Menunggu verifikasi admin.</div>
                <div style="font-weight:700; color:#888;">Silakan tunjukkan kartu member ke admin.</div>
                <a href="{{ route('member.borrowings', ['code' => $member->member_code]) }}"
                   class="btn btn-orange mt-4 fw-bold"
                   style="border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark); border-radius:0;">
                    📋 Lihat Riwayat
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

@push('custom-js')
<script>
(function () {
    const memberCode = '{{ $member->member_code }}';
    const maxSlots = {{ $member->remaining_slots }};
    let selectedBooks = [];
    let currentBookEl = null;

    function openBookDetail(el) {
        if (el.classList.contains('out-of-stock')) return;

        currentBookEl = el;
        const bookId = parseInt(el.dataset.bookId);

        // Populate modal
        document.getElementById('modalBookTitle').textContent = el.dataset.title;
        document.getElementById('modalBookAuthor').textContent = el.dataset.author;
        document.getElementById('modalBookCategory').textContent = el.dataset.category;
        document.getElementById('modalBookStock').textContent = el.dataset.stock + ' eksemplar';
        document.getElementById('modalBookIsbn').textContent = el.dataset.isbn;
        document.getElementById('modalBookDesc').textContent = el.dataset.description;

        // Cover
        const coverEl = document.getElementById('modalBookCover');
        if (el.dataset.cover) {
            coverEl.innerHTML = '<img src="' + el.dataset.cover + '" alt="cover" style="width:100%;height:100%;object-fit:cover;">';
        } else {
            coverEl.innerHTML = '<span style="font-family:\'Bangers\',cursive; font-size:3rem; color:#fff;">' + el.dataset.initial + '</span>';
        }

        // Update button: already selected?
        const isSelected = selectedBooks.some(b => b.id === bookId);
        const btn = document.getElementById('btnAddToBorrow');
        if (isSelected) {
            btn.textContent = '➖ Hapus dari Peminjaman';
            btn.style.background = 'var(--comic-red)';
        } else {
            btn.textContent = '➕ Tambahkan ke Peminjaman';
            btn.style.background = 'var(--comic-orange)';
        }

        new bootstrap.Modal(document.getElementById('bookDetailModal')).show();
    }

    // Expose to global scope so inline onclick handlers work
    window.openBookDetail = openBookDetail;

    document.getElementById('btnAddToBorrow').addEventListener('click', function () {
        if (!currentBookEl) return;
        toggleBook(currentBookEl);
        bootstrap.Modal.getInstance(document.getElementById('bookDetailModal')).hide();
    });

    function toggleBook(el) {
        if (el.classList.contains('out-of-stock')) return;
        const bookId = parseInt(el.dataset.bookId);
        const idx = selectedBooks.findIndex(b => b.id === bookId);

        if (idx >= 0) {
            selectedBooks.splice(idx, 1);
            el.classList.remove('selected');
        } else {
            if (selectedBooks.length >= maxSlots) {
                alert('Slot peminjaman habis! Maksimum ' + maxSlots + ' buku.');
                return;
            }
            selectedBooks.push({
                id: bookId,
                title: el.dataset.title,
                author: el.dataset.author,
                stock: parseInt(el.dataset.stock)
            });
            el.classList.add('selected');
        }

        updateSelectionBar();
    }

    // Expose to global scope
    window.toggleBook = toggleBook;

    function updateSelectionBar() {
        const bar = document.getElementById('selectionBar');
        const count = selectedBooks.length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('selectedCountBtn').textContent = count;
        bar.classList.toggle('d-none', count === 0);
    }

    window.clearSelection = function () {
        selectedBooks = [];
        document.querySelectorAll('.book-card-member.selected').forEach(el => el.classList.remove('selected'));
        updateSelectionBar();
    };

    window.showConfirmModal = function () {
        if (selectedBooks.length === 0) return;

        const listEl = document.getElementById('confirmBookList');
        listEl.innerHTML = selectedBooks.map(b =>
            '<div style="padding:8px 10px; border-bottom:1px solid #eee; font-size:0.85rem;">📕 ' + b.title + '<br><small style="color:#888;">' + b.author + '</small></div>'
        ).join('');
        document.getElementById('confirmError').classList.add('d-none');

        new bootstrap.Modal(document.getElementById('confirmModal')).show();
    };

    document.getElementById('btnConfirmBorrow').addEventListener('click', async function () {
        const btn = this;
        btn.disabled = true;
        btn.textContent = '⏳ Mengirim...';

        try {
            const response = await fetch('{{ route('member.borrow') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    member_code: memberCode,
                    book_ids: selectedBooks.map(b => b.id)
                })
            });

            const data = await response.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
                new bootstrap.Modal(document.getElementById('successModal')).show();
                clearSelection();
            } else {
                const errEl = document.getElementById('confirmError');
                errEl.textContent = '⚠️ ' + (data.error || 'Gagal mengajukan.');
                errEl.classList.remove('d-none');
                btn.disabled = false;
                btn.textContent = '✅ Konfirmasi Pinjam';
            }
        } catch (e) {
            document.getElementById('confirmError').textContent = '⚠️ Terjadi kesalahan. Coba lagi.';
            document.getElementById('confirmError').classList.remove('d-none');
            btn.disabled = false;
            btn.textContent = '✅ Konfirmasi Pinjam';
        }
    });
})();
</script>
@endpush