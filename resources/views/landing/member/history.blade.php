@extends('landing.layout')

@section('title', 'Riwayat Peminjaman — ' . $member->name)

@push('custom-css')
<style>
    /* ========== FILTER BUTTONS ========== */
    .filter-btn {
        border-radius: 0;
        border: 3px solid var(--comic-dark);
        box-shadow: 3px 3px 0 var(--comic-dark);
        font-family: 'Fredoka One', cursive;
        font-weight: 900;
        font-size: 0.85rem;
        padding: 8px 20px;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-block;
    }
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 4px 4px 0 var(--comic-dark);
    }
    .filter-btn.active {
        background: var(--comic-orange);
        color: #fff;
        border-color: var(--comic-orange);
    }
    .filter-btn.inactive {
        background: #fff;
        color: var(--comic-dark);
    }

    /* ========== TRANSACTION HEADER ========== */
    .txn-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        margin-bottom: 12px;
    }

    .txn-label {
        font-family: 'Fredoka One', cursive;
        font-size: 0.72rem;
        color: #888;
        letter-spacing: 2px;
    }

    /* ========== HISTORY CARD WRAPPER ========== */
    .history-card {
        background: #fff;
        border: 3px solid var(--comic-dark);
        box-shadow: 4px 4px 0 var(--comic-dark);
        padding: 16px;
        margin-bottom: 12px;
        border-radius: 0;
        transition: all 0.2s;
    }

    .history-card:hover {
        transform: translateY(-2px);
        box-shadow: 5px 5px 0 var(--comic-dark);
    }

    /* ========== HISTORY ROW ========== */
    .history-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .history-row {
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        border-bottom: none;
        padding: 0;
    }

    .history-row:last-child {
        border-bottom: none;
    }

    /* ========== BOOK COVER ========== */
    .history-cover {
        width: 80px;
        height: 110px;
        flex-shrink: 0;
        border: 3px solid var(--comic-dark);
        box-shadow: 3px 3px 0 var(--comic-dark);
        overflow: hidden;
        background: var(--comic-cream);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .history-row:hover .history-cover {
        transform: translateY(-3px);
        box-shadow: 5px 5px 0 var(--comic-dark);
    }

    .history-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .history-cover .no-cover-icon {
        font-size: 2.2rem;
        opacity: 0.4;
    }

    /* ========== BOOK INFO ========== */
    .history-info {
        flex: 1;
        min-width: 0;
    }

    .history-title {
        font-family: 'Fredoka One', cursive;
        font-size: 1rem;
        color: var(--comic-dark);
        margin-bottom: 2px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .history-author {
        font-size: 0.85rem;
        color: #666;
        font-style: italic;
        font-weight: 600;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .history-meta {
        font-size: 0.78rem;
        color: #888;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .history-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .history-code {
        font-family: 'Fredoka One', cursive;
        font-size: 0.68rem;
        color: var(--comic-orange);
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    /* ========== STATUS BADGE ========== */
    .history-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-family: 'Fredoka One', cursive;
        font-size: 0.72rem;
        padding: 4px 12px;
        border: 2px solid var(--comic-dark);
        box-shadow: 2px 2px 0 var(--comic-dark);
        font-weight: 900;
    }

    .history-status.pending {
        background: var(--comic-yellow);
        color: var(--comic-dark);
    }

    .history-status.active {
        background: var(--comic-blue);
        color: #fff;
    }

    .history-status.overdue {
        background: var(--comic-red);
        color: #fff;
        animation: pulse-red 1.5s ease-in-out infinite;
    }

    .history-status.returned {
        background: #27ae60;
        color: #fff;
    }

    @keyframes pulse-red {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }

    /* ========== ACTIONS ========== */
    .history-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex-shrink: 0;
        min-width: 120px;
        align-items: flex-end;
    }

    .btn-action {
        font-family: 'Fredoka One', cursive;
        font-size: 0.78rem;
        padding: 8px 16px;
        border: 3px solid var(--comic-dark);
        box-shadow: 3px 3px 0 var(--comic-dark);
        border-radius: 0;
        font-weight: 900;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 4px 4px 0 var(--comic-dark);
    }

    .btn-action:active {
        transform: translateY(1px);
        box-shadow: 2px 2px 0 var(--comic-dark);
    }

    .btn-action.btn-cancel {
        background: var(--comic-red);
        color: #fff;
    }

    .btn-action.btn-cancel:hover {
        background: #e02050;
    }

    .btn-action.btn-return {
        background: var(--comic-blue);
        color: #fff;
    }

    .btn-action.btn-return:hover {
        background: #3dbdb4;
    }

    .btn-action.btn-view {
        display: none;
    }

    /* ========== EMPTY STATE ========== */
    .empty-comic-box {
        background: #fff;
        border: 4px dashed var(--comic-dark);
        padding: 60px 30px;
        text-align: center;
        max-width: 480px;
        margin: 20px auto;
        box-shadow: 5px 5px 0 var(--comic-dark);
    }

    .empty-icon {
        font-size: 5rem;
        margin-bottom: 16px;
        display: block;
    }

    .empty-title {
        font-family: 'Bangers', cursive;
        font-size: 2rem;
        color: var(--comic-dark);
        letter-spacing: 2px;
        margin-bottom: 8px;
    }

    .empty-subtitle {
        font-family: 'Nunito', sans-serif;
        font-weight: 700;
        color: #888;
        margin-bottom: 24px;
    }

    .empty-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--comic-orange);
        color: #fff;
        border: 3px solid var(--comic-dark);
        box-shadow: 4px 4px 0 var(--comic-dark);
        padding: 12px 28px;
        font-family: 'Fredoka One', cursive;
        font-size: 1rem;
        font-weight: 900;
        border-radius: 0;
        text-decoration: none;
        transition: all 0.15s;
    }

    .empty-action:hover {
        background: var(--comic-yellow);
        color: var(--comic-dark);
        transform: translateY(-3px);
        box-shadow: 5px 5px 0 var(--comic-dark);
    }

    /* ========== RESPONSIVE ========== */

    /* Tablet */
    @media (min-width: 768px) {
        .history-cover {
            width: 90px;
            height: 120px;
        }

        .history-title {
            font-size: 1.05rem;
        }

        .history-actions {
            min-width: 140px;
        }

        .btn-action {
            padding: 10px 18px;
            font-size: 0.82rem;
        }
    }

    /* Desktop */
    @media (min-width: 992px) {
        .container {
            max-width: 960px;
        }

        .history-cover {
            width: 100px;
            height: 135px;
        }

        .history-info {
            padding-right: 20px;
        }

        .history-actions {
            min-width: 150px;
        }
    }

    /* Large Desktop */
    @media (min-width: 1200px) {
        .container {
            max-width: 1140px;
        }

        .history-cover {
            width: 110px;
            height: 145px;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        /* Filter Buttons */
        .d-flex.gap-2.flex-wrap.mb-4 {
            gap: 8px !important;
        }

        .filter-btn {
            font-size: 0.72rem;
            padding: 6px 12px;
            box-shadow: 2px 2px 0 var(--comic-dark);
            border: 2px solid var(--comic-dark);
        }

        /* Navbar */
        .comic-navbar-slider {
            padding: 8px 12px !important;
        }

        .brand-text {
            font-size: 1.1rem !important;
        }

        /* Hero */
        .detail-hero {
            padding: 40px 0 !important;
            min-height: auto !important;
        }

        .detail-hero .comic-section-title {
            font-size: clamp(1.4rem, 6vw, 2rem) !important;
        }

        /* History Card */
        .history-card {
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 3px 3px 0 var(--comic-dark);
            border-width: 2px;
        }

        /* History Row */
        .history-row {
            flex-wrap: wrap;
            gap: 12px;
        }

        .history-cover {
            width: 65px;
            height: 85px;
            box-shadow: 2px 2px 0 var(--comic-dark);
            border-width: 2px;
        }

        .history-info {
            flex: 1 1 calc(100% - 85px);
            min-width: 0;
        }

        .history-title {
            font-size: 0.9rem;
        }

        .history-author {
            font-size: 0.75rem;
        }

        .history-meta {
            font-size: 0.7rem;
            gap: 8px;
        }

        .history-code {
            font-size: 0.62rem;
        }

        .history-status {
            font-size: 0.65rem;
            padding: 3px 8px;
        }

        /* Actions - Horizontal */
        .history-actions {
            width: 100%;
            flex-direction: row;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-action {
            flex: 1;
            max-width: 140px;
            justify-content: center;
            font-size: 0.75rem;
            padding: 10px 12px;
            min-height: 44px;
        }
    }

    /* Extra Small Mobile */
    @media (max-width: 400px) {
        .history-cover {
            width: 55px;
            height: 72px;
        }

        .history-info {
            flex: 1 1 calc(100% - 75px);
        }

        .history-title {
            font-size: 0.82rem;
        }

        .history-author {
            font-size: 0.7rem;
        }

        .history-meta {
            font-size: 0.65rem;
            gap: 6px;
        }

        .btn-action {
            max-width: none;
            font-size: 0.72rem;
            padding: 8px 10px;
        }

        .history-actions {
            gap: 6px;
        }
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
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navHist">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navHist">
            <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.dashboard', ['code' => $member->member_code]) }}">🏠 Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.books', ['code' => $member->member_code]) }}">📖 Pinjam Buku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-dark btn-sm px-3 fw-bold" href="{{ route('member.borrowings', ['code' => $member->member_code]) }}">📋 Riwayat</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Hero Header --}}
<div class="detail-hero" style="min-height:20vh;">
    <div class="container">
        <div class="text-center text-white">
            <div class="section-label" style="color:var(--comic-yellow);">RIWAYAT</div>
            <h1 class="comic-section-title text-white mb-2">📋 RIWAYAT <span class="text-orange">PEMINJAMAN</span></h1>
            <p class="text-white-50 fw-bold">Semua aktivitas peminjaman dan pengembalian buku</p>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="container py-4">
    {{-- Filter Buttons --}}
    <div class="d-flex gap-2 flex-wrap mb-4">
        <a href="{{ route('member.borrowings', ['code' => $member->member_code]) }}"
           class="filter-btn {{ !$statusFilter ? 'active' : 'inactive' }}">📋 Semua</a>
        <a href="{{ route('member.borrowings', ['code' => $member->member_code, 'status' => 'pending']) }}"
           class="filter-btn {{ $statusFilter === 'pending' ? 'active' : 'inactive' }}">⏳ Pending</a>
        <a href="{{ route('member.borrowings', ['code' => $member->member_code, 'status' => 'active']) }}"
           class="filter-btn {{ $statusFilter === 'active' ? 'active' : 'inactive' }}">📤 Aktif</a>
        <a href="{{ route('member.borrowings', ['code' => $member->member_code, 'status' => 'returned']) }}"
           class="filter-btn {{ $statusFilter === 'returned' ? 'active' : 'inactive' }}">✅ Dikembalikan</a>
    </div>

    {{-- Borrowings List --}}
    @if($borrowings->count())
        @foreach($borrowings as $borrowing)
        @php
            $statusConfig = match($borrowing->status->value) {
                'pending'  => ['class' => 'pending', 'icon' => '⏳', 'text' => 'MENUNGGU VERIFIKASI'],
                'active'   => $borrowing->isOverdue()
                                ? ['class' => 'overdue', 'icon' => '⚠️', 'text' => "TERLAMBAT {$borrowing->daysOverdue()} HARI"]
                                : ['class' => 'active', 'icon' => '📤', 'text' => 'AKTIF'],
                'late'     => ['class' => 'overdue', 'icon' => '⚠️', 'text' => "TERLAMBAT {$borrowing->daysOverdue()} HARI"],
                'returned' => ['class' => 'returned', 'icon' => '✅', 'text' => 'DIKEMBALIKAN'],
            };
        @endphp

        <div class="history-card">
            <div class="history-row">
                {{-- Book Cover --}}
                @php
                    $firstBook = $borrowing->details->first()?->book;
                @endphp
                <div class="history-cover">
                    @if($firstBook?->cover_url)
                        <img src="{{ $firstBook->cover_url }}" alt="{{ $firstBook->title }}" loading="lazy">
                    @else
                        <span class="no-cover-icon">📖</span>
                    @endif
                </div>

                {{-- Book Info --}}
                <div class="history-info">
                    <div class="history-code">{{ $borrowing->transaction_code }}</div>
                    <div class="history-title" title="{{ $firstBook?->title }}">{{ $firstBook?->title ?? 'Buku' }}</div>
                    <div class="history-author">{{ $firstBook?->author ?? 'Unknown Author' }}</div>
                    <div class="history-meta">
                        <span>📅 Pinjam: {{ $borrowing->loan_date->translatedFormat('d M Y') }}</span>
                        @if($borrowing->status->value === 'returned' && $borrowing->return_date)
                            <span>↩️ Kembali: {{ $borrowing->return_date->translatedFormat('d M Y') }}</span>
                        @else
                            <span>📅 Tempo: {{ $borrowing->due_date->translatedFormat('d M Y') }}</span>
                        @endif
                        @if($borrowing->details->count() > 1)
                            <span>+{{ $borrowing->details->count() - 1 }} buku lagi</span>
                        @endif
                    </div>
                    <span class="history-status {{ $statusConfig['class'] }}">
                        {{ $statusConfig['icon'] }} {{ $statusConfig['text'] }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="history-actions">
                    @if($borrowing->status->value === 'pending')
                        <button type="button" class="btn-action btn-cancel"
                            onclick="cancelBorrowing({{ $borrowing->id }}, this)">
                            ❌ Batalkan
                        </button>
                    @endif

                    @if(in_array($borrowing->status->value, ['active', 'late']))
                        <a href="{{ route('member.return-qr', ['id' => $borrowing->id, 'code' => $member->member_code]) }}"
                           class="btn-action btn-return">
                            📤 Ajukan Return
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        {{-- Pagination --}}
        @if($borrowings->hasPages())
        <div class="d-flex justify-content-center mt-4">
            @include('landing.partials.pagination', ['paginator' => $borrowings, 'ariaLabel' => 'Navigasi halaman riwayat'])
        </div>
        @endif

    @else
    {{-- Empty State --}}
    <div class="empty-comic-box">
        <span class="empty-icon">📭</span>
        <div class="empty-title">TIDAK ADA RIWAYAT</div>
        <div class="empty-subtitle">Belum ada aktivitas peminjaman buku.</div>
        <a href="{{ route('member.books', ['code' => $member->member_code]) }}" class="empty-action">
            📖 Pinjam Buku Sekarang
        </a>
    </div>
    @endif
</div>

{{-- Footer --}}
<footer class="comic-footer py-4 mt-4">
    <div class="container">
        <div class="text-center text-light">
            <div style="font-family:'Fredoka One', cursive; color:var(--comic-orange); letter-spacing:2px;">
                📚 {{ app_setting('app_name', 'Perpustakaan Modern') }}
            </div>
        </div>
    </div>
</footer>
@endsection

@push('custom-js')
<script>
const memberCode = '{{ $member->member_code }}';

async function cancelBorrowing(id, btn) {
    if (!confirm('Batalkan peminjaman ini?')) return;
    btn.disabled = true;
    btn.textContent = '⏳...';
    try {
        const res = await fetch('/member/borrow/' + id + '/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ member_code: memberCode })
        });
        const data = await res.json();
        if (data.success) {
            btn.closest('.history-row').remove();
            alert('Peminjaman berhasil dibatalkan.');
        } else {
            alert(data.error || 'Gagal membatalkan.');
            btn.disabled = false;
            btn.textContent = '❌ Batalkan';
        }
    } catch (e) {
        alert('Terjadi kesalahan.');
        btn.disabled = false;
        btn.textContent = '❌ Batalkan';
    }
}
</script>
@endpush