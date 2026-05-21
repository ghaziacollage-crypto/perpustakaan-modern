@extends('layouts.app')

@section('title', 'Peminjaman Buku')
@section('page-title', 'Peminjaman Buku')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Transaksi</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Peminjaman</li>
</ul>
@endsection

@push('custom-css')
<style>
/* ── Status Filter Pills ── */
.status-filter-pills {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: nowrap;
}
.status-filter-pills .sfp-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 10px;
    border: 2px solid rgba(255,255,255,0.25);
    border-radius: 0;
    font-family: 'Fredoka One', cursive;
    font-size: 0.72rem;
    letter-spacing: 1px;
    font-weight: 900;
    text-decoration: none;
    color: rgba(255,255,255,0.65);
    background: rgba(255,255,255,0.05);
    transition: all 0.18s ease;
    white-space: nowrap;
}
.status-filter-pills .sfp-btn:hover {
    background: var(--comic-orange);
    border-color: var(--comic-orange);
    color: #fff;
    transform: translateY(-1px);
}
.status-filter-pills .sfp-btn.active {
    background: var(--comic-orange);
    border-color: var(--comic-orange);
    color: #fff;
    box-shadow: 2px 2px 0 var(--comic-dark);
}
.status-filter-pills .sfp-btn .count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    background: rgba(255,255,255,0.2);
    color: #fff;
    font-size: 0.65rem;
    font-weight: 900;
    border-radius: 0;
}
.status-filter-pills .sfp-btn.active .count-badge {
    background: var(--comic-yellow);
    color: var(--comic-dark);
}

/* ── Action Buttons ── */
.btn-action {
    min-width: 32px;
    min-height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0 !important;
    font-family: 'Fredoka One', cursive !important;
    font-size: 0.75rem !important;
    font-weight: 900 !important;
    letter-spacing: 1px;
    padding: 5px 9px !important;
    transition: all 0.2s ease;
    border: 2px solid var(--comic-dark) !important;
    box-shadow: 2px 2px 0 var(--comic-dark) !important;
}
.btn-action-sm { min-width: 28px; min-height: 28px; padding: 3px 7px !important; font-size: 0.7rem !important; }

/* ── Dropdown Aksi ── */
.aksi-dropdown .aksi-menu .aksi-item {
    text-decoration: none;
    transition: background 0.15s;
}
.aksi-dropdown .aksi-menu .aksi-item:hover {
    background: rgba(255,107,53,0.08);
}
.aksi-trigger.aktif {
    background: var(--comic-orange) !important;
    color: #fff !important;
}
.aksi-trigger.aktif i { color: #fff !important; }

/* ── Search Box ── */
.search-member-input {
    border: 2px solid rgba(255,255,255,0.3) !important;
    background: rgba(255,255,255,0.08) !important;
    color: #fff !important;
    font-weight: 800 !important;
    font-size: 0.82rem !important;
    border-radius: 0 !important;
    padding: 7px 12px !important;
    box-shadow: none !important;
}
.search-member-input::placeholder { color: rgba(255,255,255,0.45) !important; }
.search-member-input:focus {
    border-color: var(--comic-orange) !important;
    background: rgba(255,255,255,0.12) !important;
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(255,107,53,0.25) !important;
}
</style>
@endpush

@section('content')
{{-- Table Card ─────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📤 DAFTAR PEMINJAMAN</span>
        </div>
        <div class="card-toolbar d-flex align-items-center gap-2 flex-wrap">
            {{-- Search by Member ── --}}
            <form method="GET" action="{{ route('admin.borrowings.index') }}" class="d-flex gap-1 align-items-center">
                <input type="hidden" name="status" value="{{ $statusParam }}">
                <input type="text" name="member" class="search-member-input"
                    placeholder="🔍 Cari anggota..." value="{{ $searchMember ?? '' }}"
                    style="width: 150px;">
                <button type="submit" class="btn btn-sm"
                    style="background:var(--comic-orange); border:2px solid var(--comic-dark); box-shadow:2px 2px 0 var(--comic-dark); color:#fff; border-radius:0 !important; padding:7px 12px !important; font-family:'Fredoka One',cursive !important; font-size:0.78rem !important; font-weight:900 !important;">
                    <i class="ki-duotone ki-magnifier fs-5" style="color:#fff !important;"></i>
                </button>
                @if($searchMember)
                    <a href="{{ route('admin.borrowings.index', ['status' => $statusParam]) }}"
                        class="btn btn-sm"
                        style="background:#fff; border:2px solid var(--comic-red); box-shadow:2px 2px 0 var(--comic-red); color:var(--comic-red); border-radius:0 !important; padding:7px 10px !important; font-family:'Fredoka One',cursive !important; font-size:0.75rem !important; font-weight:900 !important;"
                        title="Clear">
                        <i class="ki-duotone ki-cross fs-5" style="color:var(--comic-red) !important;"></i>
                    </a>
                @endif
            </form>

            <div style="width:1px; height:28px; background:rgba(255,255,255,0.2); margin:0 4px;"></div>

            {{-- Status Filter Pills ── --}}
            <div class="status-filter-pills">
                <a href="{{ route('admin.borrowings.index') }}"
                    class="sfp-btn {{ !$statusParam ? 'active' : '' }}">
                    📋 Semua
                    <span class="count-badge">{{ $totalAll }}</span>
                </a>
                <a href="{{ route('admin.borrowings.index', ['status' => 'pending']) }}"
                    class="sfp-btn {{ $statusParam === 'pending' ? 'active' : '' }}">
                    ⏳ Menunggu Persetujuan
                    <span class="count-badge">{{ $countPending }}</span>
                </a>
                <a href="{{ route('admin.borrowings.index', ['status' => 'active']) }}"
                    class="sfp-btn {{ $statusParam === 'active' ? 'active' : '' }}">
                    📤 Aktif
                    <span class="count-badge">{{ $countActive }}</span>
                </a>
                <a href="{{ route('admin.borrowings.index', ['status' => 'late']) }}"
                    class="sfp-btn {{ $statusParam === 'late' ? 'active' : '' }}">
                    ⚠️ Terlambat
                    <span class="count-badge">{{ $countLate }}</span>
                </a>
                <a href="{{ route('admin.borrowings.index', ['status' => 'returned']) }}"
                    class="sfp-btn {{ $statusParam === 'returned' ? 'active' : '' }}">
                    ✅ Kembali
                    <span class="count-badge">{{ $countReturned }}</span>
                </a>
            </div>

            <div style="width:1px; height:28px; background:rgba(255,255,255,0.2); margin:0 4px;"></div>

            {{-- Tombol Tambah Pinjaman → ke halaman create --}}
            <a href="{{ route('admin.borrowings.create') }}"
                class="btn btn-primary d-flex align-items-center gap-1"
                style="background:var(--comic-yellow) !important; border-color:var(--comic-dark) !important; box-shadow:3px 3px 0 var(--comic-dark) !important; color:var(--comic-dark) !important; border-radius:0 !important; font-family:'Fredoka One',cursive !important; font-weight:900 !important; padding:9px 16px !important;">
                <i class="ki-duotone ki-plus fs-4" style="color:var(--comic-dark) !important;"></i>
                Tambah Pinjaman
            </a>
        </div>
    </div>

    <div class="card-body py-4 px-4">
        {{-- Table --}}
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:130px;">Kode</th>
                        <th style="min-width:160px;">Anggota</th>
                        <th style="min-width:180px;">Buku</th>
                        <th style="min-width:110px;">Tgl Pinjam</th>
                        <th style="min-width:110px;">Jatuh Tempo</th>
                        <th style="min-width:80px;">Status</th>
                        <th class="text-end" style="min-width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($borrowings as $borrowing)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark" style="font-size:0.82rem;">{{ $borrowing->transaction_code }}</span>
                            <div class="text-muted" style="font-size:0.7rem; font-weight:700;">{{ $borrowing->details->count() }} buku</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($borrowing->member->photo)
                                    <div class="symbol symbol-35px flex-shrink-0">
                                        <img src="{{ asset('storage/' . $borrowing->member->photo) }}" alt="{{ $borrowing->member->name }}"
                                            class="symbol-label" style="object-fit:cover;">
                                    </div>
                                @else
                                    <div class="symbol symbol-35px flex-shrink-0">
                                        <div class="symbol-label fs-5 fw-bold"
                                            style="background:var(--comic-cream); color:var(--comic-dark); border:2px solid var(--comic-dark);">
                                            {{ strtoupper(substr($borrowing->member->name, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size:0.85rem;">{{ $borrowing->member->name }}</span>
                                    <span class="text-muted" style="font-size:0.72rem;">
                                        NIS: {{ $borrowing->member->nis_nim ?? '-' }} | {{ $borrowing->member->member_code }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <ul class="mb-0 ps-3" style="list-style:none; padding:0;">
                                @foreach($borrowing->details->take(2) as $detail)
                                    <li style="font-size:0.8rem; color:#555; font-weight:700; display:flex; align-items:center; gap:4px;">
                                        <span style="font-size:0.75rem;">📕</span>
                                        {{ Str::limit($detail->book->title, 28) }}
                                    </li>
                                @endforeach
                                @if($borrowing->details->count() > 2)
                                    <li style="font-size:0.75rem; color:#aaa; font-weight:700;">
                                        +{{ $borrowing->details->count() - 2 }} buku lagi
                                    </li>
                                @endif
                            </ul>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size:0.82rem;">{{ $borrowing->loan_date->format('d M Y') }}</span>
                        </td>
                        <td>
                            @if($borrowing->status->value === 'late' || $borrowing->isOverdue())
                                <span class="fw-bold text-danger" style="font-size:0.82rem;">{{ $borrowing->due_date->format('d M Y') }}</span>
                                <div>
                                    <span class="badge badge-light-danger" style="font-size:0.65rem; border-radius:0 !important;">
                                        ⚠️ Terlambat {{ $borrowing->daysOverdue() }} hr
                                    </span>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:0.82rem;">{{ $borrowing->due_date->format('d M Y') }}</span>
                                <div>
                                    @php $daysLeft = (int) $borrowing->due_date->diffInDays(now()); @endphp
                                    <span class="badge {{ $daysLeft <= 2 ? 'badge-light-warning' : 'badge-light-info' }}"
                                        style="font-size:0.65rem; border-radius:0 !important;">
                                        {{ $daysLeft }} hari lagi
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'pending'  => ['class' => 'badge-light-warning', 'text' => 'Menunggu Persetujuan'],
                                    'active'  => ['class' => 'badge-light-primary', 'text' => 'Aktif'],
                                    'late'    => ['class' => 'badge-light-danger',  'text' => 'Terlambat'],
                                    'returned'=> ['class' => 'badge-light-success', 'text' => 'Kembali'],
                                ];
                                $s = $statusMap[$borrowing->status->value] ?? ['class' => 'badge-light-secondary', 'text' => ucfirst($borrowing->status->value)];
                            @endphp
                            <span class="badge {{ $s['class'] }}" style="font-size:0.72rem; border-radius:0 !important; border:2px solid currentColor !important;">{{ $s['text'] }}</span>
                        </td>
                        <td class="text-end" style="overflow:visible;">
                            <div class="d-flex justify-content-end align-items-center gap-1" style="overflow:visible;">

                                {{-- Dropdown Aksi ── --}}
                                <div class="aksi-dropdown" style="position:relative;">
                                    <button type="button"
                                        class="btn btn-sm aksi-trigger"
                                        onclick="toggleAksi(this, event)"
                                        style="background:var(--comic-dark); color:var(--comic-orange); border:2px solid var(--comic-dark);
                                               box-shadow:2px 2px 0 var(--comic-orange); border-radius:0; font-family:'Fredoka One',cursive;
                                               font-size:0.72rem; font-weight:900; letter-spacing:1px; padding:5px 12px;
                                               display:flex; align-items:center; gap:5px; transition:all 0.18s;">
                                        ⚡ AKSI
                                        <i class="ki-duotone ki-arrows-circle fs-5" style="color:var(--comic-orange);"></i>
                                    </button>
                                    <div class="aksi-menu" style="display:none; position:absolute; right:0; top:calc(100% + 4px);
                                        background:#fff; border:2px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark);
                                        min-width:160px; z-index:50; border-radius:0;">
                                        @if($borrowing->status->value === 'pending')
                                            <form method="POST" action="{{ route('admin.borrowings.approve', $borrowing) }}" class="approve-form">
                                                @csrf
                                                <button type="submit" class="aksi-item w-100"
                                                    style="display:flex; align-items:center; gap:8px; padding:9px 14px;
                                                    border:none; background:transparent; cursor:pointer; border-bottom:1px solid rgba(26,26,46,0.08);">
                                                    <i class="ki-duotone ki-check-circle fs-5" style="color:#28a745;"></i>
                                                    <span style="font-family:'Fredoka One',cursive; font-size:0.78rem; color:#28a745; font-weight:700;">Setujui Peminjaman</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.borrowings.reject', $borrowing) }}" class="reject-form">
                                                @csrf
                                                <button type="submit" class="aksi-item w-100"
                                                    style="display:flex; align-items:center; gap:8px; padding:9px 14px;
                                                    border:none; background:transparent; cursor:pointer; border-bottom:1px solid rgba(26,26,46,0.08);">
                                                    <i class="ki-duotone ki-cross-circle fs-5" style="color:#dc3545;"></i>
                                                    <span style="font-family:'Fredoka One',cursive; font-size:0.78rem; color:#dc3545; font-weight:700;">Tolak Peminjaman</span>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.borrowings.receipt', $borrowing) }}"
                                                class="aksi-item" style="display:flex; align-items:center; gap:8px; padding:9px 14px;
                                                text-decoration:none; border-bottom:1px solid rgba(26,26,46,0.08);">
                                                <i class="ki-duotone ki-doc-text fs-5" style="color:var(--comic-dark);"></i>
                                                <span style="font-family:'Fredoka One',cursive; font-size:0.78rem; color:var(--comic-dark); font-weight:700;">Lihat Struk</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.borrowings.receipt', $borrowing) }}"
                                                class="aksi-item" style="display:flex; align-items:center; gap:8px; padding:9px 14px;
                                                text-decoration:none; border-bottom:1px solid rgba(26,26,46,0.08);">
                                                <i class="ki-duotone ki-doc-text fs-5" style="color:var(--comic-dark);"></i>
                                                <span style="font-family:'Fredoka One',cursive; font-size:0.78rem; color:var(--comic-dark); font-weight:700;">Lihat Struk</span>
                                            </a>
                                            @if($borrowing->status->value === 'active')
                                                <form method="POST" action="{{ route('admin.borrowings.remind', $borrowing) }}">
                                                    @csrf
                                                    <button type="submit" class="aksi-item w-100"
                                                        onclick="return confirm('Kirim reminder WA ke {{ $borrowing->member->name }}?')"
                                                        style="display:flex; align-items:center; gap:8px; padding:9px 14px;
                                                        border:none; background:transparent; cursor:pointer; border-bottom:1px solid rgba(26,26,46,0.08);">
                                                 <i class="ki-duotone ki-message-text fs-5" style="color:#25D366;"></i>
                                                        <span style="font-family:'Fredoka One',cursive; font-size:0.78rem; color:var(--comic-dark); font-weight:700;">Kirim Reminder WA</span>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.borrowings.receipt.pdf', $borrowing) }}"
                                                target="_blank"
                                                class="aksi-item" style="display:flex; align-items:center; gap:8px; padding:9px 14px;
                                                text-decoration:none; border-bottom:none;">
                                                <i class="ki-duotone ki-file-down fs-5" style="color:var(--comic-orange);"></i>
                                                <span style="font-family:'Fredoka One',cursive; font-size:0.78rem; color:var(--comic-dark); font-weight:700;">Download PDF</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="comic-empty">
                                <span class="empty-emoji">📤</span>
                                <div class="empty-title">TIDAK ADA PEMINJAMAN</div>
                                <div class="empty-sub">
                                    @if($searchMember)
                                        Tidak ditemukan dengan kata kunci "{{ $searchMember }}"
                                    @else
                                        Mulai dengan menambahkan transaksi peminjaman baru
                                    @endif
                                </div>
                                @if(!$searchMember)
                                <a href="{{ route('admin.borrowings.create') }}"
                                    class="btn btn-comic mt-3"
                                    style="font-family:'Fredoka One', cursive !important; font-weight:900 !important;">
                                    <i class="ki-duotone ki-plus fs-4" style="color:#fff !important;"></i>
                                    Tambah Pinjaman
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('layouts.partials._pagination', ['paginator' => $borrowings])
    </div>
</div>
@endsection

@push('custom-js')
<script>
// Toggle aksi dropdown - defined globally first
function toggleAksi(btn, e) {
    e.stopPropagation();
    var menu = btn.closest('.aksi-dropdown').querySelector('.aksi-menu');
    var isOpen = menu.style.display === 'block';

    // Close all
    document.querySelectorAll('.aksi-menu').forEach(function (m) { m.style.display = 'none'; });
    document.querySelectorAll('.aksi-trigger').forEach(function (b) { b.classList.remove('aktif'); });

    if (!isOpen) {
        menu.style.display = 'block';
        btn.classList.add('aktif');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Focus search on Ctrl+K
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const input = document.querySelector('input[name="member"]');
            if (input) input.focus();
        }
    });

    // Close all dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.aksi-dropdown')) {
            document.querySelectorAll('.aksi-menu').forEach(function (menu) {
                menu.style.display = 'none';
            });
            document.querySelectorAll('.aksi-trigger').forEach(function (btn) {
                btn.classList.remove('aktif');
            });
        }
    });

    // Confirm approve/reject forms
    document.querySelectorAll('.approve-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Setujui peminjaman ini? Stok buku akan dikurangi dan notifikasi WhatsApp akan dikirim.')) {
                e.preventDefault();
            }
        });
    });

    document.querySelectorAll('.reject-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Tolak peminjaman ini? Data akan dihapus permanen dan notifikasi WhatsApp akan dikirim.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
