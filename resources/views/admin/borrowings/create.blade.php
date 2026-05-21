@extends('layouts.app')

@section('title', 'Tambah Peminjaman')
@section('page-title', 'Tambah Peminjaman')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.borrowings.index') }}" class="text-muted text-hover-primary">Peminjaman</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Tambah</li>
</ul>
@endsection


@push('custom-css')
<style>
/* ── Step Progress ── */
.step-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 28px;
}
.step-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.step-circle {
    width: 40px; height: 40px;
    border-radius: 0 !important;
    border: 3px solid rgba(255,255,255,0.3) !important;
    background: rgba(255,255,255,0.08) !important;
    color: rgba(255,255,255,0.5) !important;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Fredoka One', cursive !important;
    font-size: 1.1rem !important;
    font-weight: 900 !important;
    transition: all 0.3s ease;
    flex-shrink: 0;
}
.step-circle.active {
    background: var(--comic-orange) !important;
    border-color: var(--comic-yellow) !important;
    color: #fff !important;
    box-shadow: 0 0 0 4px rgba(255,107,53,0.3), 4px 4px 0 var(--comic-dark) !important;
    transform: scale(1.1);
}
.step-circle.done {
    background: var(--comic-green) !important;
    border-color: #fff !important;
    color: #fff !important;
}
.step-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.7rem;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase;
    transition: color 0.3s;
    white-space: nowrap;
}
.step-label.active { color: var(--comic-orange) !important; }
.step-connector {
    width: 60px; height: 3px;
    background: rgba(255,255,255,0.15);
    margin: 0 8px;
    transition: background 0.3s;
}
.step-connector.done { background: var(--comic-green) !important; }

/* ── Step Content ── */
.step-content { display: none; animation: fadeInStep 0.3s ease; }
.step-content.active { display: block; }
@keyframes fadeInStep {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ── Scanner Card ── */
.qr-scanner-card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 5px 5px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
    background: #fff;
}
.qr-scanner-card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
    padding: 12px 20px;
}
.qr-scanner-card .card-header .card-title {
    font-family: 'Bangers', cursive !important;
    letter-spacing: 2px !important;
    color: var(--comic-orange) !important;
    font-size: 1.1rem !important;
}

/* ── Member Card ── */
.member-display-card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 5px 5px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
    background: #fff;
}
.member-display-card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
}
.slot-meter {
    display: flex;
    gap: 6px;
    margin-top: 8px;
}
.slot-dot {
    width: 28px; height: 28px;
    border: 2px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Fredoka One', cursive !important;
    font-size: 0.9rem !important;
    font-weight: 900 !important;
    transition: all 0.3s ease;
}
.slot-dot.used {
    background: var(--comic-orange) !important;
    color: #fff !important;
}
.slot-dot.empty {
    background: rgba(255,255,255,0.15) !important;
    color: rgba(255,255,255,0.4) !important;
}

/* ── Active Borrowings ── */
.borrowing-book-card {
    display: flex;
    gap: 12px;
    padding: 12px;
    border: 2px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    background: #fff;
    margin-bottom: 8px;
    transition: all 0.2s;
}
.borrowing-book-card:hover { transform: translateX(4px); box-shadow: 5px 5px 0 var(--comic-dark); }
.borrowing-book-card .book-cover {
    width: 50px; height: 65px;
    object-fit: cover;
    border: 2px solid var(--comic-dark);
    flex-shrink: 0;
    background: var(--comic-cream);
    display: flex; align-items: center; justify-content: center;
}
.borrowing-book-card .book-cover img { width: 100%; height: 100%; object-fit: cover; }
.borrowing-book-card .book-meta { font-size: 0.78rem; color: #888; font-weight: 700; }

/* ── Selected Books ── */
.selected-book-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border: 2px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    background: #fff;
    margin-bottom: 8px;
    transition: all 0.2s;
    animation: slideInBook 0.25s ease;
}
.selected-book-card:hover { transform: translateX(4px); box-shadow: 5px 5px 0 var(--comic-dark); }
@keyframes slideInBook {
    from { opacity: 0; transform: translateX(-12px); }
    to { opacity: 1; transform: translateX(0); }
}
.selected-book-card .book-cover {
    width: 50px; height: 65px;
    object-fit: cover;
    border: 2px solid var(--comic-dark);
    flex-shrink: 0;
    background: var(--comic-cream);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.selected-book-card .book-cover img { width: 100%; height: 100%; object-fit: cover; }
.selected-book-card .book-num {
    width: 28px; height: 28px;
    background: var(--comic-orange);
    border: 2px solid var(--comic-dark);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Bangers', cursive;
    font-size: 1rem;
    color: #fff;
    flex-shrink: 0;
}
.btn-remove-book {
    background: #fff !important;
    border: 2px solid var(--comic-red) !important;
    box-shadow: 2px 2px 0 var(--comic-red) !important;
    border-radius: 0 !important;
    color: var(--comic-red) !important;
    min-width: 34px; min-height: 34px;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.btn-remove-book:hover {
    background: var(--comic-red) !important;
    color: #fff !important;
    transform: translateY(-2px);
}

/* ── Manual Input ── */
.manual-input-group {
    display: flex;
    gap: 8px;
}
.manual-input-group .form-control {
    border: 2px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    font-weight: 800 !important;
}
.manual-input-group .form-control:focus {
    border-color: var(--comic-orange) !important;
    box-shadow: 3px 3px 0 var(--comic-orange) !important;
}

/* ── Summary ── */
.summary-card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 5px 5px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
    background: #fff;
}
.summary-card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
}
.summary-book-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px dashed #ddd;
    font-size: 0.85rem;
}
.summary-book-item:last-child { border-bottom: none; }

/* ── Alerts ── */
.alert-warning-slots {
    background: #fff3cd;
    border: 2px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    box-shadow: 3px 3px 0 var(--comic-dark) !important;
    font-weight: 800;
    font-size: 0.82rem;
}

/* ── Camera Select ── */
.camera-select {
    border: 2px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    font-weight: 800 !important;
    font-size: 0.8rem !important;
}

/* ── #qr-reader styling ── */
#qr-reader { border: 3px solid var(--comic-dark) !important; box-shadow: 4px 4px 0 var(--comic-dark) !important; border-radius: 0 !important; }
#qr-reader video { border-radius: 0 !important; }

/* ── QR Reader styling ── */
</style>
@endpush

@section('content')
<div class="row g-5">
    {{-- LEFT COLUMN: Scanner + Input + Summary --}}
    <div class="col-lg-5">

        {{-- STEP PROGRESS ── --}}
        <div class="step-progress mb-4">
            <div class="step-item">
                <div class="step-circle active" id="step-1-circle">1</div>
                <span class="step-label active" id="step-1-label">SCAN MEMBER</span>
            </div>
            <div class="step-connector" id="conn-1-2"></div>
            <div class="step-item">
                <div class="step-circle" id="step-2-circle">2</div>
                <span class="step-label" id="step-2-label">SCAN BUKU</span>
            </div>
            <div class="step-connector" id="conn-2-3"></div>
            <div class="step-item">
                <div class="step-circle" id="step-3-circle">3</div>
                <span class="step-label" id="step-3-label">KONFIRMASI</span>
            </div>
        </div>

        {{-- STEP 1: Scan Member ─────────────────────────────────────────── --}}
        <div class="step-content active" id="step-1">
            <div class="card qr-scanner-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title">
                        👤 <span style="font-family:'Bangers',cursive; letter-spacing:1px;">SCAN QR MEMBER</span>
                    </div>
                    <select id="camera-select-member" class="form-select camera-select" style="width:auto; max-width:160px;">
                        <option value="">📷 Kamera</option>
                    </select>
                </div>
                <div class="card-body p-3">
                    <div id="qr-reader-member" style="width: 100%;"></div>
                    <div id="member-scan-msg" class="text-center mt-2" style="font-family:'Fredoka One',cursive; font-size:0.8rem; color:#aaa;"></div>

                    <div style="border-top:2px dashed #ddd; margin: 16px 0;"></div>

                    <div style="font-family:'Fredoka One', cursive; font-size:0.75rem; color:var(--comic-orange); letter-spacing:2px; margin-bottom:8px;">
                        🔢 INPUT MANUAL KODE MEMBER
                    </div>
                    <div class="manual-input-group">
                        <input type="text" id="manual-member-code" class="form-control"
                            placeholder="Ketik kode member..." style="font-weight:800;">
                        <button type="button" class="btn btn-comic" id="btn-lookup-member" style="padding:9px 14px !important;">
                            <i class="ki-duotone ki-magnifier fs-5" style="color:#fff !important;"></i>
                        </button>
                    </div>
                    <div id="member-error" class="text-danger mt-2 d-none"
                        style="font-weight:800; font-size:0.8rem; font-family:'Fredoka One', cursive;"></div>
                </div>
            </div>
        </div>

        {{-- STEP 2: Scan Books ─────────────────────────────────────────── --}}
        <div class="step-content" id="step-2">
            <div class="card qr-scanner-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title">
                        📕 <span style="font-family:'Bangers',cursive; letter-spacing:1px;">SCAN QR BUKU</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-light-primary" id="slot-remaining-badge"
                            style="font-size:0.78rem; font-family:'Fredoka One', cursive; border-radius:0 !important; border:2px solid currentColor !important;">
                            Slot: <span id="slot-remaining-num">3</span>/3
                        </span>
                        <select id="camera-select-book" class="form-select camera-select" style="width:auto; max-width:160px;">
                            <option value="">📷 Kamera</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div id="qr-reader-book" style="width: 100%;"></div>
                    <div id="book-scan-msg" class="text-center mt-2" style="font-family:'Fredoka One',cursive; font-size:0.8rem; color:#aaa;"></div>

                    <div style="border-top:2px dashed #ddd; margin: 16px 0;"></div>

                    <div style="font-family:'Fredoka One', cursive; font-size:0.75rem; color:var(--comic-orange); letter-spacing:2px; margin-bottom:8px;">
                        🔢 INPUT MANUAL KODE BUKU
                    </div>
                    <div class="manual-input-group">
                        <input type="text" id="manual-book-code" class="form-control"
                            placeholder="Ketik kode buku..." style="font-weight:800;">
                        <button type="button" class="btn btn-comic" id="btn-lookup-book" style="padding:9px 14px !important;">
                            <i class="ki-duotone ki-magnifier fs-5" style="color:#fff !important;"></i>
                        </button>
                    </div>
                    <div id="book-error" class="text-danger mt-2 d-none"
                        style="font-weight:800; font-size:0.8rem; font-family:'Fredoka One', cursive;"></div>
                </div>
            </div>

            {{-- Selected Books List --}}
            <div class="card qr-scanner-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title" style="font-family:'Bangers',cursive; letter-spacing:2px; color:var(--comic-orange);">
                        📕 BUKU DIPILIH
                    </div>
                    <span class="badge badge-light-primary" id="book-count-badge"
                        style="font-size:0.8rem; font-family:'Fredoka One', cursive; border-radius:0 !important; border:2px solid currentColor !important;">
                        <span id="selected-count">0</span>/<span id="max-slots">3</span>
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="selected-books-list"></div>

                    {{-- Tombol Lanjut ke Konfirmasi ── --}}
                    <div style="border-top:3px solid var(--comic-dark); margin-top:12px; padding-top:12px;">
                        <button type="button" id="btn-go-to-confirm" class="btn btn-comic w-100"
                            style="font-size:0.95rem !important; padding:12px !important; letter-spacing:1px; background:var(--comic-yellow) !important; color:var(--comic-dark) !important;"
                            onclick="goToConfirmStep()">
                            <i class="ki-duotone ki-arrow-right fs-5" style="color:var(--comic-dark) !important;"></i>
                            LANJUT KE KONFIRMASI
                        </button>
                        <div style="font-family:'Fredoka One', cursive; font-size:0.72rem; color:#aaa; text-align:center; margin-top:6px;">
                            Pilih buku lalu klik tombol di atas
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 3: Confirmation ─────────────────────────────────────────── --}}
        <div class="step-content" id="step-3">
            <div class="card summary-card">
                <div class="card-header">
                    <div class="card-title" style="font-family:'Bangers',cursive; letter-spacing:2px; color:var(--comic-orange);">
                        ✅ KONFIRMASI & SIMPAN
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="borrowing-form">
                        @csrf

                        <input type="hidden" name="member_id" id="form-member-id">
                        <input type="hidden" name="due_date" id="form-due-date">

                        <div class="mb-3">
                            <label class="form-label" style="font-family:'Fredoka One', cursive; font-size:0.78rem; letter-spacing:1px; color:var(--comic-orange);">
                                👤 ANGGOTA
                            </label>
                            <div id="summary-member" class="p-3" style="background:var(--comic-cream); border:2px solid var(--comic-dark); font-weight:800; font-size:0.88rem;"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label" style="font-family:'Fredoka One', cursive; font-size:0.78rem; letter-spacing:1px; color:var(--comic-orange);">
                                    📅 TGL PINJAM
                                </label>
                                <div id="summary-loan-date" class="p-3" style="background:var(--comic-cream); border:2px solid var(--comic-dark); font-weight:800; font-size:0.88rem;"></div>
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-family:'Fredoka One', cursive; font-size:0.78rem; letter-spacing:1px; color:var(--comic-orange);">
                                    ⏰ JATUH TEMPO
                                </label>
                                <div id="summary-due-date" class="p-3" style="background:var(--comic-cream); border:2px solid var(--comic-dark); font-weight:800; font-size:0.88rem;"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-family:'Fredoka One', cursive; font-size:0.78rem; letter-spacing:1px; color:var(--comic-orange);">
                                📕 DAFTAR BUKU (<span id="summary-book-count">0</span> buku)
                            </label>
                            <div id="summary-books" class="p-3" style="background:var(--comic-cream); border:2px solid var(--comic-dark);"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-family:'Fredoka One', cursive; font-size:0.78rem; letter-spacing:1px; color:var(--comic-orange);">
                                📝 CATATAN (opsional)
                            </label>
                            <textarea name="notes" id="form-notes" class="form-control" rows="2"
                                placeholder="Catatan peminjaman..." style="font-weight:700; border:2px solid var(--comic-dark); border-radius:0; box-shadow:3px 3px 0 var(--comic-dark);"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary flex-fill" onclick="goToStep(2)"
                                style="font-family:'Fredoka One', cursive !important; font-weight:900 !important; border-radius:0 !important; border:2px solid var(--comic-dark) !important; box-shadow:3px 3px 0 var(--comic-dark) !important;">
                                <i class="ki-duotone ki-arrow-left fs-5" style="color:var(--comic-dark) !important;"></i>
                                KEMBALI
                            </button>
                            <button type="submit" id="btn-submit-borrowing" class="btn btn-comic flex-fill"
                                style="font-size:1rem !important; padding:12px !important; letter-spacing:1px;">
                                <i class="ki-duotone ki-check fs-5" style="color:#fff !important;"></i>
                                SIMPAN PEMINJAMAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN: Member Info + Active Borrowings ─────────────── --}}
    <div class="col-lg-7">

        {{-- Member Info Card --}}
        <div class="card member-display-card mb-4" id="member-info-card" style="display:none;">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title" style="font-family:'Bangers',cursive; letter-spacing:2px; color:var(--comic-orange);">
                    👤 DATA ANGGOTA
                </div>
                <div id="member-status-badge"></div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start gap-4">
                    <div id="member-avatar" class="flex-shrink-0"
                        style="width:70px; height:70px; border:3px solid var(--comic-dark); display:flex; align-items:center; justify-content:center; font-family:'Bangers',cursive; font-size:2rem; background:var(--comic-cream); color:var(--comic-dark);">
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-family:'Bangers',cursive; font-size:1.4rem; letter-spacing:2px; color:var(--comic-dark);"
                            id="member-display-name"></div>
                        <div style="font-family:'Fredoka One', cursive; font-size:0.8rem; color:#aaa; letter-spacing:1px;"
                            id="member-display-code"></div>

                        <div class="mt-3">
                            <div style="font-family:'Fredoka One', cursive; font-size:0.68rem; color:var(--comic-orange); letter-spacing:2px; text-transform:uppercase; margin-bottom:6px;">
                                SLOT PEMINJAMAN
                            </div>
                            <div class="slot-meter" id="slot-meter"></div>
                        </div>

                        <div class="d-flex gap-4 mt-3" style="font-family:'Fredoka One', cursive; font-size:0.75rem;">
                            <div>
                                <span style="color:var(--comic-orange);">📤</span>
                                <span style="font-weight:900; color:var(--comic-dark);" id="member-active-count">0</span>
                                <span style="color:#aaa;">sedang dipinjam</span>
                            </div>
                            <div>
                                <span style="color:var(--comic-green);">📥</span>
                                <span style="font-weight:900; color:var(--comic-dark);" id="member-remaining-slots">3</span>
                                <span style="color:#aaa;">sisa slot</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="member-limit-warning" class="alert alert-warning-slots mt-3 d-none">
                    <i class="ki-duotone ki-information-5 fs-5" style="color:var(--comic-dark);"></i>
                    &nbsp;Anggota sudah mencapai <strong>batas maksimal 3 buku</strong>. Tidak dapat menambah peminjaman.
                </div>
            </div>
        </div>

        {{-- Active Borrowings of Member --}}
        <div class="card member-display-card mb-4" id="active-borrowings-card" style="display:none;">
            <div class="card-header">
                <div class="card-title" style="font-family:'Bangers',cursive; letter-spacing:2px; color:var(--comic-orange);">
                    📚 BUKU SEDANG DIPINJAM
                </div>
            </div>
            <div class="card-body" id="active-borrowings-list">
                {{-- Filled by JS --}}
            </div>
        </div>

        {{-- Step 1 Empty State: Scan to view member --}}
        <div class="card member-display-card" id="member-empty-state">
            <div class="card-body text-center py-10">
                <div style="font-size:4rem; margin-bottom:16px;">👤</div>
                <div style="font-family:'Bangers',cursive; font-size:1.4rem; letter-spacing:2px; color:var(--comic-dark);">
                    SCAN QR MEMBER TERLEBIH DAHULU
                </div>
                <div style="font-family:'Fredoka One', cursive; font-size:0.82rem; color:#aaa; margin-top:8px;">
                    Scan kartu member atau ketik kode secara manual untuk memulai transaksi
                </div>

                {{-- Diagnostic Test Button --}}
                <div style="margin-top:24px; padding:16px; background:#f0f0f0; border:2px dashed #ccc; text-align:left;">
                    <div style="font-family:'Fredoka One', cursive; font-size:0.72rem; color:#888; letter-spacing:1px; margin-bottom:8px;">🧪 DIAGNOSTIC — TEST MANUAL</div>
                    <input type="text" id="diag-test-code" class="form-control mb-2"
                        placeholder="Ketik kode member untuk test..." style="font-weight:800; border:2px solid #333;">
                    <button type="button" class="btn btn-comic w-100" onclick="diagTestLookup()" style="font-family:'Fredoka One', cursive !important; font-weight:900 !important;">
                        🔍 TEST CARI MEMBER
                    </button>
                    <div id="diag-result" class="mt-2" style="font-family:monospace; font-size:0.75rem; color:#555; min-height:40px; word-break:break-all;"></div>
                </div>
            </div>
        </div>

        {{-- Step 2 Help Card --}}
        <div class="card member-display-card" id="step2-help" style="display:none;">
            <div class="card-header">
                <div class="card-title" style="font-family:'Bangers',cursive; letter-spacing:2px; color:var(--comic-orange);">
                    💡 CARA MENAMBAH BUKU
                </div>
            </div>
            <div class="card-body" style="font-family:'Fredoka One', cursive; font-size:0.82rem; color:#555; line-height:1.8;">
                <div class="mb-2">📷 <strong>Scan QR</strong> di sampul buku</div>
                <div class="mb-2">⌨️ <strong>Ketik kode</strong> buku secara manual</div>
                <div>📋 <strong>Maksimum</strong> <span id="step2-max-slots">3</span> buku per transaksi</div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('vendor-js')
<script src="{{ asset('vendor/qrcode/html5-qrcode.min.js') }}"></script>
<script src="{{ asset('vendor/qrcode/sweetalert2.all.min.js') }}"></script>
@endpush

@push('custom-js')
<script>
(function () {
    'use strict';

    // ── State ──────────────────────────────────────────────────────────────────
    let currentStep = 1;
    let currentMember = null;
    let selectedBooks = [];
    let memberScanner = null;
    let bookScanner = null;
    let scanDebounceTimer = null;
    let camerasDetected = false;

    // ── Settings ────────────────────────────────────────────────────────────────
    const MAX_SLOTS = 3;
    const DEFAULT_DUE_DATE = @json($defaultDueDate);
    const LOAN_DURATION = @json($loanDuration);

    // ── Step Navigation ─────────────────────────────────────────────────────────
    window.goToStep = function (step) {
        currentStep = step;
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');

        // Update step circles
        for (let i = 1; i <= 3; i++) {
            const circle = document.getElementById('step-' + i + '-circle');
            const label = document.getElementById('step-' + i + '-label');
            circle.classList.remove('active', 'done');
            label.classList.remove('active');

            if (i < step) {
                circle.classList.add('done');
                circle.textContent = '✓';
            } else if (i === step) {
                circle.classList.add('active');
                label.classList.add('active');
            }
        }

        // Connectors
        if (step >= 2) document.getElementById('conn-1-2').classList.add('done');
        else document.getElementById('conn-1-2').classList.remove('done');
        if (step >= 3) document.getElementById('conn-2-3').classList.add('done');
        else document.getElementById('conn-2-3').classList.remove('done');

        // Start scanner on step 2 if not started
        if (step === 2 && !bookScanner) {
            setTimeout(() => initBookScanner(), 300);
        }

        // Populate summary on step 3
        if (step === 3) populateSummary();
    };

    // ── Member Lookup ──────────────────────────────────────────────────────────
    async function lookupMember(code) {
        if (!code) return;

        console.log('[SCAN] QR Code decoded:', JSON.stringify(code));
        console.log('[SCAN] Code length:', code.length, '| First 5 chars:', code.substring(0, 5));

        try {
            const url = `/admin/borrowings/lookup-member?code=${encodeURIComponent(code)}`;
            console.log('[SCAN] Fetching URL:', url);

            const res = await fetch(url);
            const data = await res.json();

            console.log('[SCAN] Response status:', res.status, '| success:', data.success);

            if (!res.ok || !data.success) {
                console.error('[SCAN] API Error:', data.error || 'Unknown error');
                showMemberError(data.error || `HTTP ${res.status}: Member tidak ditemukan`);
                return;
            }

            currentMember = data.member;
            console.log('[SCAN] Member found:', currentMember.name, '| Code:', currentMember.member_code);
            showMemberCard(currentMember);
            document.getElementById('member-error').classList.add('d-none');
        } catch (err) {
            console.error('[SCAN] Fetch error:', err);
            showMemberError('Gagal mengambil data: ' + (err.message || err.toString()));
        }
    }

    function showMemberError(msg) {
        const el = document.getElementById('member-error');
        el.textContent = '⚠️ ' + msg;
        el.classList.remove('d-none');
        Swal.fire({ icon: 'error', title: 'Error', text: msg, toast: true, position: 'top-end', timer: 3000 });
    }

    function showMemberCard(member) {
        document.getElementById('member-empty-state').style.display = 'none';
        document.getElementById('member-info-card').style.display = 'block';
        document.getElementById('active-borrowings-card').style.display = 'block';
        document.getElementById('step2-help').style.display = 'block';

        document.getElementById('step2-max-slots').textContent = member.remaining_slots;

        const nameEl = document.getElementById('member-display-name');
        nameEl.textContent = member.name;

        const codeEl = document.getElementById('member-display-code');
        codeEl.textContent = 'NIS: ' + (member.nis_nim || '-') + ' | ' + member.member_code + (member.class ? ' • ' + member.class : '');

        const avatarEl = document.getElementById('member-avatar');
        if (member.photo) {
            avatarEl.innerHTML = `<img src="${member.photo}" alt="${member.name}" style="width:100%;height:100%;object-fit:cover;">`;
        } else {
            avatarEl.textContent = member.name.charAt(0).toUpperCase();
        }

        const statusBadge = document.getElementById('member-status-badge');
        if (member.status === 'active') {
            statusBadge.innerHTML = `<span class="badge badge-light-success" style="font-size:0.72rem; border-radius:0; border:2px solid currentColor !important;">✅ AKTIF</span>`;
        } else {
            statusBadge.innerHTML = `<span class="badge badge-light-danger" style="font-size:0.72rem; border-radius:0; border:2px solid currentColor !important;">❌ TIDAK AKTIF</span>`;
        }

        // Slot meter
        const meterEl = document.getElementById('slot-meter');
        let slotsHtml = '';
        for (let i = 0; i < MAX_SLOTS; i++) {
            const used = i < member.active_borrowings_count;
            slotsHtml += `<div class="slot-dot ${used ? 'used' : 'empty'}">${used ? '📤' : ''}</div>`;
        }
        meterEl.innerHTML = slotsHtml;

        document.getElementById('member-active-count').textContent = member.active_borrowings_count;
        document.getElementById('member-remaining-slots').textContent = member.remaining_slots;
        document.getElementById('slot-remaining-num').textContent = member.remaining_slots;
        document.getElementById('max-slots').textContent = member.remaining_slots;

        // Limit warning
        const warnEl = document.getElementById('member-limit-warning');
        if (member.remaining_slots <= 0) {
            warnEl.classList.remove('d-none');
            // Disable book scanning
            document.getElementById('manual-book-code').disabled = true;
            document.getElementById('btn-lookup-book').disabled = true;
            if (bookScanner) stopScanner(bookScanner, 'qr-reader-book');
        } else {
            warnEl.classList.add('d-none');
            document.getElementById('manual-book-code').disabled = false;
            document.getElementById('btn-lookup-book').disabled = false;
        }

        // Active borrowings list
        const listEl = document.getElementById('active-borrowings-list');
        if (member.active_borrowings && member.active_borrowings.length > 0) {
            listEl.innerHTML = member.active_borrowings.map(b => `
                <div class="mb-3">
                    <div style="font-family:'Fredoka One', cursive; font-size:0.72rem; color:var(--comic-orange); letter-spacing:1px; margin-bottom:6px;">
                        📋 ${b.transaction_code} &nbsp;|&nbsp; 📅 ${b.loan_date} → ⏰ ${b.due_date}
                        ${b.is_overdue ? '<span class="badge badge-light-danger" style="font-size:0.65rem; border-radius:0; border:2px solid currentColor !important;">⚠️ TERLAMBAT</span>' : ''}
                    </div>
                    ${b.books.map(book => `
                        <div class="borrowing-book-card">
                            <div class="book-cover">
                                ${book.cover ? `<img src="${book.cover}" alt="${book.title}">` : '📕'}
                            </div>
                            <div>
                                <div style="font-family:'Fredoka One', cursive; font-weight:900; font-size:0.85rem; color:var(--comic-dark);">📕 ${book.title}</div>
                                <div class="book-meta">${book.book_code}</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `).join('');
        } else {
            listEl.innerHTML = `
                <div class="text-center py-4" style="font-family:'Fredoka One', cursive; font-size:0.85rem; color:#aaa;">
                    Tidak ada buku sedang dipinjam
                </div>`;
        }

        // Auto advance to step 2
        goToStep(2);
    }

    // ── Book Lookup ─────────────────────────────────────────────────────────────
    async function lookupBook(code) {
        if (!code) return;
        clearTimeout(scanDebounceTimer);
        scanDebounceTimer = setTimeout(async () => {
            console.log('[BOOK] QR Code decoded:', JSON.stringify(code));

            try {
                const res = await fetch(`/admin/borrowings/lookup-book?code=${encodeURIComponent(code)}`);
                const data = await res.json();

                console.log('[BOOK] Response status:', res.status, '| success:', data.success);

                if (!res.ok || !data.success) {
                    console.error('[BOOK] API Error:', data.error || 'Unknown error');
                    showBookError(data.error || `HTTP ${res.status}: Buku tidak ditemukan`);
                    return;
                }

                addBook(data.book);
                document.getElementById('book-error').classList.add('d-none');
            } catch (err) {
                console.error('[BOOK] Fetch error:', err);
                showBookError('Gagal mengambil data buku: ' + (err.message || err.toString()));
            }
        }, 300);
    }

    function showBookError(msg) {
        const el = document.getElementById('book-error');
        el.textContent = '⚠️ ' + msg;
        el.classList.remove('d-none');
    }

    function addBook(book) {
        // Check duplicate
        if (selectedBooks.find(b => b.id === book.id)) {
            Swal.fire({ icon: 'warning', title: 'Sudah Ditambahkan', text: `"${book.title}" sudah ada di daftar.`, toast: true, position: 'top-end', timer: 2000 });
            return;
        }

        // Check slot
        if (selectedBooks.length >= (currentMember?.remaining_slots ?? 0)) {
            Swal.fire({ icon: 'warning', title: 'Slot Penuh', text: `Maksimal ${currentMember.remaining_slots} buku.`, toast: true, position: 'top-end', timer: 2500 });
            return;
        }

        selectedBooks.push(book);
        updateBooksUI();

        document.getElementById('book-error').classList.add('d-none');
        document.getElementById('manual-book-code').value = '';
    }

    window.removeBook = function (bookId) {
        selectedBooks = selectedBooks.filter(b => b.id !== bookId);
        updateBooksUI();
    };

    function updateBooksUI() {
        const container = document.getElementById('selected-books-list');
        const countEl = document.getElementById('selected-count');
        const badgeEl = document.getElementById('book-count-badge');
        const maxSlots = currentMember?.remaining_slots ?? MAX_SLOTS;

        countEl.textContent = selectedBooks.length;
        badgeEl.className = 'badge ' + (selectedBooks.length >= maxSlots ? 'badge-light-danger' : 'badge-light-primary') +
            '" style="font-size:0.8rem; font-family:\'Fredoka One\', cursive; border-radius:0 !important; border:2px solid currentColor !important;"';

        if (selectedBooks.length === 0) {
            container.innerHTML = `
                <div class="text-center py-6" style="font-family:'Fredoka One', cursive; font-size:0.85rem; color:#aaa;">
                    Scan atau ketik kode buku untuk menambahkan
                </div>`;
            return;
        }

        container.innerHTML = selectedBooks.map((book, i) => `
            <div class="selected-book-card">
                <div class="book-num">${i + 1}</div>
                <div class="book-cover">
                    ${book.cover ? `<img src="${book.cover}" alt="${book.title}">` : '📕'}
                </div>
                <div class="flex-grow-1" style="min-width:0;">
                    <div style="font-family:'Fredoka One', cursive; font-weight:900; font-size:0.85rem; color:var(--comic-dark);">📕 ${book.title}</div>
                    <div style="font-family:'Fredoka One', cursive; font-size:0.72rem; color:#aaa;">${book.book_code} ${book.author ? '• ' + book.author : ''}</div>
                </div>
                <button type="button" class="btn btn-remove-book" onclick="removeBook(${book.id})" title="Hapus">
                    <i class="ki-duotone ki-trash fs-5" style="color:var(--comic-red) !important;"></i>
                </button>
            </div>
        `).join('');
    }

    // ── Summary & Submit ────────────────────────────────────────────────────────
    function populateSummary() {
        if (!currentMember) return;

        document.getElementById('form-member-id').value = currentMember.id;
        document.getElementById('form-due-date').value = DEFAULT_DUE_DATE;

        const today = new Date();
        const loanStr = today.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const dueDateObj = new Date(DEFAULT_DUE_DATE);
        const dueStr = dueDateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

        document.getElementById('summary-member').innerHTML = `
            <span style="font-family:'Fredoka One', cursive; font-size:1rem; color:var(--comic-dark);">${currentMember.name}</span>
            <span style="font-family:'Fredoka One', cursive; font-size:0.78rem; color:#aaa; margin-left:8px;">${currentMember.member_code}</span>`;

        document.getElementById('summary-loan-date').textContent = loanStr;
        document.getElementById('summary-due-date').textContent = dueStr;
        document.getElementById('summary-book-count').textContent = selectedBooks.length;

        const booksHtml = selectedBooks.map((b, i) => `
            <div class="summary-book-item">
                <span style="font-family:'Fredoka One', cursive; color:var(--comic-orange);">${i + 1}.</span>
                <span style="font-family:'Fredoka One', cursive; font-weight:900; color:var(--comic-dark); flex-grow:1;">📕 ${b.title}</span>
                <span style="font-family:'Fredoka One', cursive; font-size:0.72rem; color:#aaa;">${b.book_code}</span>
            </div>
        `).join('');
        document.getElementById('summary-books').innerHTML = booksHtml;
    }

    // ── Confirm Step Button ────────────────────────────────────────────────────
    window.goToConfirmStep = function () {
        if (selectedBooks.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Buku!',
                text: 'Pilih setidaknya satu buku sebelum melanjutkan.',
                confirmButtonText: 'OK',
                confirmButtonColor: 'var(--comic-orange)',
            });
            return;
        }
        goToStep(3);
    };

    document.getElementById('borrowing-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        if (selectedBooks.length === 0) {
            Swal.fire({ icon: 'error', title: 'Belum Ada Buku', text: 'Tambahkan setidaknya satu buku.' });
            return;
        }

        try {
            const res = await fetch('/admin/borrowings', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    member_id: currentMember.id,
                    book_ids: selectedBooks.map(b => b.id),
                    due_date: DEFAULT_DUE_DATE,
                    notes: document.getElementById('form-notes').value,
                }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                const receiptUrl = data.data?.receipt_url;
                await Swal.fire({
                    icon: 'success',
                    title: '✅ BERHASIL!',
                    html: 'Peminjaman berhasil disimpan.<br>Mau cetak struk sekarang?',
                    confirmButtonText: '🖨️ Ya, Cetak Struk',
                    cancelButtonText: 'Tidak, Nanti Saja',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--comic-orange)',
                    cancelButtonColor: '#aaa',
                }).then(result => {
                    if (result.isConfirmed && receiptUrl) {
                        window.location.href = receiptUrl;
                    } else {
                        window.location.href = '{{ route('admin.borrowings.index') }}';
                    }
                });
            } else {
                const errMsg = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Terjadi kesalahan');
                Swal.fire({ icon: 'error', title: 'GAGAL', text: errMsg });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan koneksi.' });
        }
    });

    // ── QR Scanner ──────────────────────────────────────────────────────────────
    function handleMemberScan(decodedText) {
        console.log('[QR-SCAN] Member QR decoded:', JSON.stringify(decodedText), '| length:', decodedText.length);
        const msgEl = document.getElementById('member-scan-msg');
        if (msgEl) {
            msgEl.textContent = '✅ QR Terdeteksi!';
            msgEl.style.color = 'var(--comic-green)';
        }
        lookupMember(decodedText);
        setTimeout(() => {
            const msg = document.getElementById('member-scan-msg');
            if (msg) { msg.textContent = ''; msg.style.color = ''; }
        }, 2500);
    }

    function handleBookScan(decodedText) {
        console.log('[QR-SCAN] Book QR decoded:', JSON.stringify(decodedText), '| length:', decodedText.length);
        const msgEl = document.getElementById('book-scan-msg');
        if (msgEl) {
            msgEl.textContent = '✅ Buku Terdeteksi!';
            msgEl.style.color = 'var(--comic-green)';
        }
        lookupBook(decodedText);
        setTimeout(() => {
            const msg = document.getElementById('book-scan-msg');
            if (msg) { msg.textContent = ''; msg.style.color = ''; }
        }, 2000);
    }

    function stopAllScanners() {
        if (memberScanner) { memberScanner.stop().catch(() => {}); memberScanner = null; }
        if (bookScanner) { bookScanner.stop().catch(() => {}); bookScanner = null; }
    }

    async function initMemberScanner() {
        const el = document.getElementById('qr-reader-member');
        if (!el) return;

        // First request permission then get devices
        let devices = [];
        try {
            // Request camera permission by trying to get stream
            const testStream = await navigator.mediaDevices.getUserMedia({ video: true });
            testStream.getTracks().forEach(t => t.stop()); // Stop test stream
            devices = await Html5Qrcode.getCameras();
        } catch (permErr) {
            console.error('Camera error detail:', permErr);

            // Cek apakah ada kamera terdeteksi tapi izin ditolak
            let camerasFound = [];
            try {
                camerasFound = await Html5Qrcode.getCameras();
            } catch {}

            if (camerasFound && camerasFound.length > 0) {
                // Kamera ada tapi izin ditolak (sudah di-deny sebelumnya)
                el.innerHTML = `
                    <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                        <div style="font-size:2.5rem; margin-bottom:8px;">🔒</div>
                        <strong style="color:var(--comic-dark); font-size:1rem;">Izin Kamera Ditolak</strong>
                        <div style="color:#666; margin-top:8px; font-size:0.78rem; font-weight:700;">
                            Kamera terdeteksi tapi akses ditolak.<br>
                            Ikuti langkah di bawah untuk mengaktifkan:
                        </div>
                        <div style="margin-top:12px; text-align:left; padding:10px 16px; background:#fff; border:2px solid #ddd; font-size:0.75rem; color:#555;">
                            <strong>Langkah Reset Izin Kamera:</strong><br>
                            1. Tekan <strong>🔒</strong> di address bar (kiri URL)<br>
                            2. Atau buka: <code>edge://settings/content/camera</code><br>
                            3. Ubah jadi <strong>"Allow"</strong> untuk 127.0.0.1 / localhost<br>
                            4. Refresh halaman ini<br>
                            5. Atau klik tombol di bawah ↓
                        </div>
                        <button id="btn-reset-permission" type="button" class="btn btn-comic mt-3"
                            style="font-family:'Fredoka One', cursive !important; font-weight:900 !important; font-size:0.85rem !important; padding:10px 20px !important;">
                            🔓 BUKA SETTINGS KAMERA
                        </button>
                        <div style="margin-top:10px; font-size:0.72rem; color:#888;">
                            Atau gunakan <strong>input manual</strong> ↓
                        </div>
                    </div>`;

                document.getElementById('btn-reset-permission')?.addEventListener('click', () => {
                    window.open('edge://settings/content/camera', '_blank');
                });

                // Tampilkan dropdown kamera yang terdeteksi
                const select = document.getElementById('camera-select-member');
                select.innerHTML = '<option value="">📷 Kamera Terdeteksi</option>';
                camerasFound.forEach((d, i) => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.label || `Kamera ${i + 1}`;
                    select.appendChild(opt);
                });
                select.disabled = false;
                return;
            }

            // Tidak ada kamera sama sekali
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">📷</div>
                    <strong style="color:var(--comic-dark); font-size:1rem;">Kamera Tidak Terdeteksi</strong>
                    <div style="color:#666; margin-top:8px; font-size:0.78rem; font-weight:700;">
                        Error: ${permErr?.message || permErr?.name || permErr?.toString() || 'Tidak dapat mengakses kamera'}
                    </div>
                    <div style="margin-top:10px; font-size:0.72rem; color:#888;">
                        Pastikan webcam terhubung dan bukan di-sandbox.<br>
                        Atau gunakan <strong>input manual</strong> ↓
                    </div>
                </div>`;
            return;
        }

        // ── DIAGNOSTIC: cek kamera tanpa trigger izin ──
        window.diagnoseCamera = async function () {
            const resultDiv = document.createElement('div');
            resultDiv.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:99999;background:#1A1A2E;color:#fff;padding:16px;font-family:monospace;font-size:13px;border-bottom:3px solid var(--comic-orange);max-height:300px;overflow:auto;';
            document.body.appendChild(resultDiv);

            let html = '<strong style="color:var(--comic-orange);font-size:1.1rem;">🔍 KAMERA DIAGNOSTIC</strong><br><br>';

            try {
                const cameras = await Html5Qrcode.getCameras();
                html += `✅ Cameras found: <strong>${cameras.length}</strong><br>`;
                cameras.forEach((c, i) => {
                    html += `  Camera ${i + 1}: [${c.id}] ${c.label || 'tanpa label'}<br>`;
                });

                // Coba start scanner
                memberScanner = new Html5Qrcode('qr-reader-member');
                await memberScanner.start(
                    cameras[0].id,
                    { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    (txt) => { html += `✅ QR DETECTED: ${txt}<br>`; resultDiv.innerHTML = html; },
                    () => {}
                );
                html += `✅ Scanner started successfully with camera ID: <strong>${cameras[0].id}</strong><br>`;
                html += `<br>📷 Kamera ALREADY WORKING di atas! Tutup diagnostic ini ↓<br>`;
                html += `<button onclick="this.parentElement.remove()" style="background:var(--comic-orange);color:#fff;border:2px solid #000;padding:6px 14px;cursor:pointer;font-weight:bold;margin-top:8px;">TUTUP DIAGNOSTIC</button>`;
            } catch (err) {
                html += `❌ Error: <strong style="color:#ff6b6b;">${err?.message || err?.name || err}</strong><br>`;
                html += `<br>💡 Ini artinya browser belum memberikan izin kamera.<br>`;
                html += `Tekan 🔒 di address bar → Allow kamera → Refresh.<br>`;
                html += `<br><button onclick="this.parentElement.remove()" style="background:#555;color:#fff;border:2px solid #000;padding:6px 14px;cursor:pointer;">TUTUP</button>`;
            }

            resultDiv.innerHTML = html;
        };

        if (!devices || devices.length === 0) {
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">📷</div>
                    <strong style="color:var(--comic-dark);">Kamera Tidak Ditemukan</strong>
                    <div style="color:#666; margin-top:6px; font-size:0.78rem; font-weight:700;">
                        Tidak ada kamera yang tersedia di perangkat ini<br>
                        Gunakan <strong>input manual</strong> sebagai alternatif
                    </div>
                </div>`;
            return;
        }

        // Found cameras - populate select
        camerasDetected = true;
        const select = document.getElementById('camera-select-member');
        devices.forEach((d, i) => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = d.label || `Kamera ${i + 1}`;
            select.appendChild(opt);
        });
        select.disabled = false;

        // Start scanner with first available camera
        const cameraId = devices[0].id;
        memberScanner = new Html5Qrcode('qr-reader-member');

        const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };

        try {
            await memberScanner.start(
                cameraId,
                config,
                handleMemberScan,
                (errMsg) => {} // Ignore scan failures (no QR found)
            );
        } catch (startErr) {
            console.warn('Scanner start error:', startErr);
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">📷</div>
                    <strong style="color:var(--comic-dark);">Gagal Memulai Kamera</strong>
                    <div style="color:#666; margin-top:6px; font-size:0.78rem; font-weight:700;">
                        Error: ${startErr?.message || startErr || 'Tidak dapat memulai scanner'}
                    </div>
                </div>`;
        }
    }

    async function initBookScanner() {
        const el = document.getElementById('qr-reader-book');
        if (!el || bookScanner) return;

        // Get cameras
        let devices = [];
        try {
            const testStream = await navigator.mediaDevices.getUserMedia({ video: true });
            testStream.getTracks().forEach(t => t.stop());
            devices = await Html5Qrcode.getCameras();
        } catch (permErr) {
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">🔒</div>
                    <strong style="color:var(--comic-dark);">Izin Kamera Ditolak</strong>
                    <div style="color:#666; margin-top:6px; font-size:0.78rem; font-weight:700;">
                        Gunakan <strong>input manual</strong> sebagai alternatif
                    </div>
                </div>`;
            return;
        }

        if (!devices || devices.length === 0) {
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">📷</div>
                    <strong style="color:var(--comic-dark);">Kamera Tidak Ditemukan</strong>
                    <div style="color:#666; margin-top:6px; font-size:0.78rem; font-weight:700;">
                        Gunakan <strong>input manual</strong> sebagai alternatif
                    </div>
                </div>`;
            return;
        }

        camerasDetected = true;
        const select = document.getElementById('camera-select-book');
        devices.forEach((d, i) => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = d.label || `Kamera ${i + 1}`;
            select.appendChild(opt);
        });
        select.disabled = false;

        bookScanner = new Html5Qrcode('qr-reader-book');
        const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };

        try {
            await bookScanner.start(
                devices[0].id,
                config,
                handleBookScan,
                (errMsg) => {}
            );
        } catch (startErr) {
            console.warn('Book scanner start error:', startErr);
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">📷</div>
                    <strong style="color:var(--comic-dark);">Gagal Memulai Kamera</strong>
                    <div style="color:#666; margin-top:6px; font-size:0.78rem; font-weight:700;">
                        Gunakan <strong>input manual</strong> sebagai alternatif
                    </div>
                </div>`;
        }
    }

    // Camera select change handler - restart scanner with selected camera
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('camera-select-member')?.addEventListener('change', async function () {
            const cameraId = this.value;
            if (!cameraId || !memberScanner) return;
            try {
                await memberScanner.stop();
            } catch {}
            try {
                await memberScanner.start(cameraId, { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    handleMemberScan, () => {});
            } catch {}
        });

        document.getElementById('camera-select-book')?.addEventListener('change', async function () {
            const cameraId = this.value;
            if (!cameraId || !bookScanner) return;
            try {
                await bookScanner.stop();
            } catch {}
            try {
                await bookScanner.start(cameraId, { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    handleBookScan, () => {});
            } catch {}
        });
    });

    // ── Manual Input Bindings ────────────────────────────────────────────────────
    document.getElementById('btn-lookup-member').addEventListener('click', () => {
        const code = document.getElementById('manual-member-code').value.trim();
        if (code) lookupMember(code);
    });

    document.getElementById('manual-member-code').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (code) lookupMember(code);
        }
    });

    document.getElementById('btn-lookup-book').addEventListener('click', () => {
        const code = document.getElementById('manual-book-code').value.trim();
        if (code) lookupBook(code);
    });

    document.getElementById('manual-book-code').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (code) lookupBook(code);
        }
    });

    // ── Init ────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async function () {
        // Diag: check if Html5Qrcode is loaded
        console.log('[INIT] typeof Html5Qrcode:', typeof Html5Qrcode);
        console.log('[INIT] typeof window.Html5Qrcode:', typeof window.Html5Qrcode);
        console.log('[INIT] typeof window.__Html5QrcodeLibrary__:', typeof window.__Html5QrcodeLibrary__);
        console.log('[INIT] Library keys available:', Object.keys(window).filter(k => k.toLowerCase().includes('html') || k.toLowerCase().includes('qr')).join(', '));

        if (typeof Html5Qrcode === 'undefined' && typeof window.__Html5QrcodeLibrary__ === 'undefined') {
            const errEl = document.getElementById('qr-reader-member');
            if (errEl) {
                errEl.innerHTML = `
                    <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-red); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                        <div style="font-size:2.5rem; margin-bottom:8px;">⚠️</div>
                        <strong style="color:var(--comic-red); font-size:1rem;">Library QR Scanner Gagal Dimuat</strong>
                        <div style="color:#666; margin-top:8px; font-size:0.78rem; font-weight:700;">
                            Html5Qrcode tidak tersedia di browser.<br>
                            Pastikan file <code>vendor/qrcode/html5-qrcode.min.js</code> ada.<br>
                            <br>
                            Error: Html5Qrcode is not defined
                        </div>
                    </div>`;
            }
            return;
        }

        // Camera select change handlers
        document.getElementById('camera-select-member')?.addEventListener('change', async function () {
            const cameraId = this.value;
            if (!cameraId || !memberScanner) return;
            try { await memberScanner.stop(); } catch {}
            try {
                await memberScanner.start(cameraId, { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    handleMemberScan, () => {});
            } catch {}
        });

        document.getElementById('camera-select-book')?.addEventListener('change', async function () {
            const cameraId = this.value;
            if (!cameraId || !bookScanner) return;
            try { await bookScanner.stop(); } catch {}
            try {
                await bookScanner.start(cameraId, { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    handleBookScan, () => {});
            } catch {}
        });

        await initMemberScanner();
        updateBooksUI();
    });

    // Cleanup on page leave
    window.addEventListener('beforeunload', () => {
        if (memberScanner) memberScanner.stop().catch(() => {});
        if (bookScanner) bookScanner.stop().catch(() => {});
    });

    // ── Diagnostic Functions (callable from browser console) ─────────────────
    window.diagTestLookup = async function () {
        const code = document.getElementById('diag-test-code')?.value.trim();
        const resultEl = document.getElementById('diag-result');
        if (!code) {
            if (resultEl) resultEl.innerHTML = '<span style="color:red;">⚠️ Masukkan kode member dulu!</span>';
            return;
        }
        if (resultEl) resultEl.innerHTML = '⏳ Mencari: <strong>' + code + '</strong>...';
        try {
            const res = await fetch(`/admin/borrowings/lookup-member?code=${encodeURIComponent(code)}`);
            const data = await res.json();
            if (res.ok && data.success) {
                if (resultEl) resultEl.innerHTML = `<span style="color:green;">✅ DITEMUKAN: ${data.member.name} (${data.member.member_code})<br>Slot: ${data.member.remaining_slots}/3 | Aktif: ${data.member.status}</span>`;
            } else {
                if (resultEl) resultEl.innerHTML = `<span style="color:red;">❌ GAGAL: HTTP ${res.status} — ${data.error || 'Tidak ditemukan'}</span>`;
            }
        } catch (err) {
            if (resultEl) resultEl.innerHTML = `<span style="color:red;">❌ FETCH ERROR: ${err.message}</span>`;
        }
    };

    // Diagnostic camera test (call from console: diagnoseCamera())
    window.diagnoseCamera = async function () {
        const diag = document.createElement('div');
        diag.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:99999;background:#1A1A2E;color:#fff;padding:20px;font-family:monospace;font-size:13px;max-height:80vh;overflow:auto;border-bottom:4px solid var(--comic-orange);';
        document.body.appendChild(diag);

        let html = '<strong style="color:var(--comic-orange);font-size:1.2rem;">🔍 KAMERA DIAGNOSTIC TOOL</strong><br><br>';

        // Check library
        html += `typeof Html5Qrcode: <strong>${typeof Html5Qrcode}</strong><br>`;
        html += `typeof __Html5QrcodeLibrary__: <strong>${typeof window.__Html5QrcodeLibrary__}</strong><br><br>`;

        if (typeof Html5Qrcode === 'undefined' && typeof window.__Html5QrcodeLibrary__ === 'undefined') {
            html += `<span style="color:#ff6b6b;">❌ Html5Qrcode TIDAK tersedia! Library tidak dimuat.</span><br><br>`;
            html += `<strong>Possible causes:</strong><br>`;
            html += `• Tracking Prevention memblokir file JS<br>`;
            html += `• File tidak ditemukan di server<br>`;
            html += `• Browser tidak mengizinkan akses kamera<br><br>`;
            html += `<button onclick="this.parentElement.remove()" style="background:var(--comic-orange);color:#fff;border:2px solid #000;padding:8px 16px;cursor:pointer;font-weight:bold;">TUTUP</button>`;
            diag.innerHTML = html;
            return;
        }

        // Check camera
        try {
            const cameras = await Html5Qrcode.getCameras();
            html += `✅ Cameras found: <strong>${cameras.length}</strong><br>`;
            cameras.forEach((c, i) => {
                html += `  ${i + 1}. [${c.id}] <strong>${c.label || 'Tanpa Label'}</strong><br>`;
            });

            if (cameras.length > 0) {
                html += `<br>✅ Kamera tersedia! Scanner seharusnya bisa aktif.<br>`;
                html += `<button onclick="this.parentElement.remove()" style="background:var(--comic-green);color:#fff;border:2px solid #000;padding:8px 16px;cursor:pointer;font-weight:bold;">TUTUP — KAMERA AKTIF!</button>`;
            } else {
                html += `<br>⚠️ Tidak ada kamera terdeteksi.<br>`;
                html += `<button onclick="this.parentElement.remove()" style="background:#555;color:#fff;border:2px solid #000;padding:8px 16px;cursor:pointer;">TUTUP</button>`;
            }
        } catch (camErr) {
            html += `<span style="color:#ff6b6b;">❌ Camera Error: ${camErr.message || camErr}</span><br>`;
            html += `<button onclick="this.parentElement.remove()" style="background:#555;color:#fff;border:2px solid #000;padding:8px 16px;cursor:pointer;">TUTUP</button>`;
        }

        diag.innerHTML = html;
    };

    // Helper to start scanner properly using the correct library constructor
    window.startScanner = async function (elementId, onScanCallback) {
        if (typeof Html5Qrcode === 'undefined') {
            // Try from library wrapper
            const lib = window.__Html5QrcodeLibrary__;
            if (!lib || !lib.Html5Qrcode) {
                alert('Html5Qrcode library not available');
                return;
            }
            const scanner = new lib.Html5Qrcode(elementId);
            try {
                const cameras = await scanner.getCameras();
                if (!cameras || cameras.length === 0) {
                    alert('Tidak ada kamera terdeteksi');
                    return;
                }
                await scanner.start(
                    cameras[0].id,
                    { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    (decodedText) => onScanCallback(decodedText),
                    () => {}
                );
                alert('Scanner aktif!');
            } catch (e) {
                alert('Gagal memulai scanner: ' + e.message);
            }
        } else {
            const scanner = new Html5Qrcode(elementId);
            try {
                const cameras = await scanner.getCameras();
                if (!cameras || cameras.length === 0) {
                    alert('Tidak ada kamera terdeteksi');
                    return;
                }
                await scanner.start(
                    cameras[0].id,
                    { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    (decodedText) => onScanCallback(decodedText),
                    () => {}
                );
                alert('Scanner aktif!');
            } catch (e) {
                alert('Gagal memulai scanner: ' + e.message);
            }
        }
    };

})();
</script>
@endpush