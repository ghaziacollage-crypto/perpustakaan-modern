@extends('landing.layout')

@section('title', 'Dashboard — ' . $member->name)

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-icon">📚</span>
            <span class="brand-text fw-black">{{ app_setting('app_name', 'Perpustakaan') }}</span>
        </a>
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navDash">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navDash">
            <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.dashboard', ['code' => $member->member_code]) }}">🏠 Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.books', ['code' => $member->member_code]) }}">📖 Pinjam Buku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.borrowings', ['code' => $member->member_code]) }}">📋 Riwayat</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-warning btn-sm px-3 fw-bold text-dark" href="{{ route('member.index') }}">🔄 Scan Baru</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Hero Header --}}
<div class="detail-hero" style="min-height: 25vh;">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div style="width:70px; height:70px; border-radius:50%; border:3px solid var(--comic-orange); overflow:hidden; background:var(--comic-orange); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                @if($member->photo)
                    <img src="{{ asset('storage/' . $member->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:2rem;">👤</span>
                @endif
            </div>
            <div>
                <div class="section-label" style="color: var(--comic-yellow);">MEMBER AREA</div>
                <h1 class="comic-section-title text-white mb-1">👋 Hai, <span class="text-orange">{{ $member->name }}</span>!</h1>
                <p class="text-white-50 fw-bold mb-0" style="font-size:0.9rem;">
                    NIS: {{ $member->nis_nim ?? '-' }} | {{ $member->class ?? '-' }} {{ $member->major ? '| ' . $member->major : '' }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="container py-5">
    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div style="background:#fff; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark); padding:20px; text-align:center;">
                <div style="font-family:'Bangers',cursive; font-size:2.5rem; color:var(--comic-blue);">{{ $activeBorrowings->count() }}</div>
                <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">SEDANG PINJAM</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div style="background:#fff; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark); padding:20px; text-align:center;">
                <div style="font-family:'Bangers',cursive; font-size:2.5rem; color:var(--comic-yellow);">{{ $pendingCount }}</div>
                <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">MENUNGGU</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div style="background:#fff; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark); padding:20px; text-align:center;">
                <div style="font-family:'Bangers',cursive; font-size:2.5rem; color:var(--comic-orange);">{{ $member->remaining_slots }}</div>
                <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">SISA SLOT</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div style="background:#fff; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark); padding:20px; text-align:center;">
                <div style="font-family:'Bangers',cursive; font-size:2.5rem; color:var(--comic-red);">{{ $lateCount }}</div>
                <div style="font-size:0.7rem; font-weight:900; color:#aaa; letter-spacing:2px;">TERLAMBAT</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-5">
        <div class="col-md-6">
            <a href="{{ route('member.books', ['code' => $member->member_code]) }}" class="text-decoration-none">
                <div style="background:var(--comic-orange); border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark); padding:30px; text-align:center;">
                    <div style="font-size:3rem; margin-bottom:10px;">📖</div>
                    <div style="font-family:'Bangers',cursive; font-size:1.5rem; color:#fff; letter-spacing:2px;">PINJAM BUKU BARU</div>
                    <div style="font-size:0.8rem; color:rgba(255,255,255,0.7); font-weight:700; margin-top:5px;">Pilih buku yang ingin dipinjam</div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('member.borrowings', ['code' => $member->member_code]) }}" class="text-decoration-none">
                <div style="background:var(--comic-dark); border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-orange); padding:30px; text-align:center;">
                    <div style="font-size:3rem; margin-bottom:10px;">📋</div>
                    <div style="font-family:'Bangers',cursive; font-size:1.5rem; color:var(--comic-orange); letter-spacing:2px;">RIWAYAT PEMINJAMAN</div>
                    <div style="font-size:0.8rem; color:rgba(255,255,255,0.7); font-weight:700; margin-top:5px;">Lihat & ajukan pengembalian</div>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Active Borrowings --}}
    @if($activeBorrowings->count())
    <div class="card" style="border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark);">
        <div class="card-header" style="background:var(--comic-dark); border-bottom:4px solid var(--comic-orange);">
            <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:var(--comic-orange); letter-spacing:2px;">
                📤 PEMINJAMAN AKTIF
            </div>
        </div>
        <div class="card-body" style="background:var(--comic-cream); padding:0;">
            <div class="table-responsive">
                <table class="table mb-0" style="border:none;">
                    <thead style="background:rgba(26,26,46,0.05);">
                        <tr>
                            <th style="border:none; font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; color:#888;">KODE</th>
                            <th style="border:none; font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; color:#888;">TANGGAL</th>
                            <th style="border:none; font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; color:#888;">BUKU</th>
                            <th style="border:none; font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; color:#888;">STATUS</th>
                            <th style="border:none; font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; color:#888;">JATUH TEMPO</th>
                            <th style="border:none; font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; color:#888;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeBorrowings as $borrowing)
                        <tr style="border-bottom:1px solid rgba(26,26,46,0.08);">
                            <td style="border:none; font-weight:800; font-size:0.85rem;">{{ $borrowing->transaction_code }}</td>
                            <td style="border:none; font-size:0.82rem; color:#888;">{{ $borrowing->loan_date->format('d M Y') }}</td>
                            <td style="border:none; font-size:0.82rem;">{{ $borrowing->details->count() }} buku</td>
                            <td style="border:none;">
                                @php
                                    $s = $borrowing->status->value === 'late' || $borrowing->isOverdue()
                                        ? ['bg' => 'var(--comic-red)', 't' => 'TERLAMBAT']
                                        : ['bg' => 'var(--comic-blue)', 't' => 'AKTIF'];
                                @endphp
                                <span class="badge" style="background:{{ $s['bg'] }}; color:#fff; border:2px solid var(--comic-dark); border-radius:0; font-size:0.72rem;">
                                    {{ $s['t'] }}
                                </span>
                            </td>
                            <td style="border:none; font-size:0.82rem; color:{{ $borrowing->isOverdue() ? 'var(--comic-red)' : '#888' }}; font-weight:700;">
                                {{ $borrowing->due_date->format('d M Y') }}
                            </td>
                            <td style="border:none;">
                                <a href="{{ route('member.return-qr', ['id' => $borrowing->id, 'code' => $member->member_code]) }}"
                                   class="btn btn-sm fw-bold"
                                   style="background:var(--comic-green); color:#fff; border:2px solid var(--comic-dark); box-shadow:2px 2px 0 var(--comic-dark); border-radius:0; font-size:0.75rem; text-decoration:none;">
                                    📤 Ajukan Return
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="empty-comic-box text-center">
        <div class="empty-icon">📚</div>
        <h3 class="fw-black">BELUM ADA PEMINJAMAN AKTIF</h3>
        <p class="fw-bold text-muted mb-3">Yuk pinjam buku baru!</p>
        <a href="{{ route('member.books', ['code' => $member->member_code]) }}" class="btn btn-orange fw-black px-4">
            📖 Pinjam Buku
        </a>
    </div>
    @endif
</div>

{{-- Footer --}}
<footer class="comic-footer py-4 mt-5">
    <div class="container">
        <div class="text-center text-light">
            <div style="font-family:'Fredoka One',cursive; color:var(--comic-orange); letter-spacing:2px;">
                📚 {{ app_setting('app_name', 'Perpustakaan Modern') }}
            </div>
            <div style="font-size:0.8rem; color:rgba(255,255,255,0.5); margin-top:5px;">
                {{ now()->format('Y') }} — All Rights Reserved
            </div>
        </div>
    </div>
</footer>
@endsection

@push('custom-js')
<script>
(function () {
    const memberCode = @json($member->member_code);
    let activeSignature = @json($activeBorrowings->pluck('id')->sort()->values()->implode(','));

    async function refreshWhenBorrowingsChange() {
        try {
            const response = await fetch("{{ route('member.lookup') }}?code=" + encodeURIComponent(memberCode), {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store'
            });
            const json = await response.json();
            if (!json.success) return;

            const ids = (json.borrowings || [])
                .filter(item => item.status === 'active' || item.status === 'late')
                .map(item => item.id)
                .sort((a, b) => a - b)
                .join(',');

            if (ids !== activeSignature) {
                window.location.reload();
            }
        } catch (_) {
            // Keep the dashboard usable if polling fails.
        }
    }

    setInterval(refreshWhenBorrowingsChange, 8000);
})();
</script>
@endpush
