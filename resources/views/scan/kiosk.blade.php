@extends('layouts.app')

@section('title', 'Scan Perpustakaan — Kiosk Mode')
@section('page-title', '📷 Scan Perpustakaan')

{{-- Full screen layout for kiosk --}}
@prepend('body.class', 'kiosk-mode')

@push('vendor-css')
<link rel="stylesheet" href="https://unpkg.com/html5-qrcode@2.0.3/min/html5-qrcode.min.css">
<style>
body.kiosk-mode {
    overflow: hidden;
    height: 100vh;
}
.kiosk-header {
    background: var(--comic-dark);
    border-bottom: 5px solid var(--comic-orange);
    padding: 10px 0;
}
.kiosk-step {
    background: #fff;
    border: 3px solid var(--comic-dark);
    box-shadow: 4px 4px 0 var(--comic-dark);
    border-radius: 0;
    padding: 20px;
    text-align: center;
}
.kiosk-step.active {
    border-color: var(--comic-orange);
    box-shadow: 4px 4px 0 var(--comic-orange);
}
.kiosk-step.done {
    background: #e8f8e8;
    border-color: #27ae60;
}
.step-number {
    width: 50px; height: 50px;
    border: 3px solid var(--comic-dark);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Bangers', cursive;
    font-size: 1.5rem;
    margin: 0 auto 10px;
    background: var(--comic-cream);
}
.kiosk-step.active .step-number {
    background: var(--comic-orange);
    color: #fff;
    border-color: var(--comic-orange);
}
.kiosk-step.done .step-number {
    background: #27ae60;
    color: #fff;
    border-color: #27ae60;
}
.kiosk-book-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #fff;
    border: 3px solid var(--comic-dark);
    box-shadow: 4px 4px 0 var(--comic-dark);
    margin-bottom: 12px;
    transition: all 0.2s;
}
.kiosk-book-card:hover { transform: translateX(5px); }
.book-num-kiosk {
    width: 45px; height: 45px;
    background: var(--comic-orange);
    border: 2px solid var(--comic-dark);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Bangers', cursive;
    font-size: 1.5rem;
    color: #fff;
    flex-shrink: 0;
}
.kiosk-book-info strong { font-family: 'Fredoka One', cursive; font-size: 1rem; }
.kiosk-book-info small { color: #888; font-weight: 700; }
.kiosk-success {
    background: #e8f8e8;
    border: 4px solid #27ae60;
    box-shadow: 6px 6px 0 #27ae60;
}
.kiosk-error {
    background: #fff0f0;
    border: 4px solid var(--comic-red);
}
</style>
@endpush

@section('content')
<div class="kiosk-header">
    <div class="container d-flex align-items-center justify-content-between">
        <div>
            <span style="font-family: 'Bangers', cursive; font-size: 1.5rem; color: var(--comic-orange); letter-spacing: 2px;">
                📚 {{ app_setting('app_name', 'Perpustakaan') }}
            </span>
            <span style="font-size: 0.8rem; color: rgba(255,255,255,0.6); font-weight: 700; margin-left: 10px;">
                — Mode Kiosk
            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span id="current-time" style="font-family: 'Fredoka One', cursive; font-size: 1.2rem; color: var(--comic-orange);">--:--:--</span>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm fw-bold" style="border-radius:0;">
                ← Kembali
            </a>
        </div>
    </div>
</div>

<div class="container-fluid py-4" style="height: calc(100vh - 70px); overflow-y: auto;">
    <div class="row g-4">
        {{-- LEFT: Scanner + Steps --}}
        <div class="col-lg-5">
            {{-- Step Indicator --}}
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="kiosk-step active" id="step-1">
                        <div class="step-number">1</div>
                        <div style="font-family:'Fredoka One', cursive; font-size:0.85rem;">Scan Member</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="kiosk-step" id="step-2">
                        <div class="step-number">2</div>
                        <div style="font-family:'Fredoka One', cursive; font-size:0.85rem;">Scan Buku</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="kiosk-step" id="step-3">
                        <div class="step-number">3</div>
                        <div style="font-family:'Fredoka One', cursive; font-size:0.85rem;">Ajukan</div>
                    </div>
                </div>
            </div>

            {{-- Scanner Card --}}
            <div class="card" id="scanner-card" style="border: 4px solid var(--comic-dark); box-shadow: 6px 6px 0 var(--comic-dark); border-radius:0;">
                <div class="card-header" style="background: var(--comic-dark); border-bottom: 4px solid var(--comic-orange);">
                    <div class="card-title" id="scanner-title" style="font-family:'Bangers', cursive; font-size:1.2rem; letter-spacing:2px; color:var(--comic-orange);">
                        📷 SCAN QR MEMBER
                    </div>
                </div>
                <div class="card-body" style="background: var(--comic-cream);">
                    <div id="qr-reader-kiosk" style="width: 100%;"></div>

                    <div class="text-center my-3">
                        <div style="font-family:'Fredoka One', cursive; color:#aaa; letter-spacing:2px; font-size:0.8rem;">— ATAU INPUT MANUAL —</div>
                    </div>

                    <div class="d-flex gap-2 mb-2">
                        <input type="text" id="kiosk-manual-input" class="form-control form-control-lg"
                            placeholder="Ketik kode..." style="font-weight:800; border: 3px solid var(--comic-dark); border-radius:0;"
                            data-code-type="member">
                        <button type="button" class="btn btn-orange btn-lg fw-bold" id="btn-kiosk-lookup"
                            style="border-radius:0; border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark);">
                            🔍
                        </button>
                    </div>
                    <div id="kiosk-error" class="d-none p-3" style="font-weight:700; color: var(--comic-red); background: #fff0f0; border: 2px solid var(--comic-red);"></div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Current Member + Books List --}}
        <div class="col-lg-7">
            {{-- Current Member --}}
            <div class="card mb-3" id="member-card" style="border:4px solid var(--comic-dark); box-shadow:6px 6px 0 var(--comic-dark); border-radius:0;">
                <div class="card-header" style="background: var(--comic-dark);">
                    <div class="card-title" style="font-family:'Bangers', cursive; font-size:1.2rem; letter-spacing:2px; color:var(--comic-orange);">
                        👤 MEMBER SAAT INI
                    </div>
                </div>
                <div class="card-body" style="background: var(--comic-cream);" id="member-card-body">
                    <div class="text-center py-5">
                        <div style="font-size:3rem; margin-bottom:10px;">👤</div>
                        <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:#aaa; letter-spacing:2px;">BELUM ADA MEMBER</div>
                        <div style="font-size:0.85rem; color:#aaa; font-weight:700;">Scan QR member terlebih dahulu</div>
                    </div>
                </div>
            </div>

            {{-- Books List --}}
            <div class="card" id="books-card" style="border:4px solid var(--comic-dark); box-shadow:6px 6px 0 var(--comic-dark); border-radius:0;">
                <div class="card-header" style="background: var(--comic-dark); border-bottom:4px solid var(--comic-orange);">
                    <div class="card-title" style="font-family:'Bangers',cursive; font-size:1.2rem; letter-spacing:2px; color:var(--comic-orange);">
                        📕 BUKU DIPILIH
                    </div>
                    <span id="books-count-kiosk" class="badge badge-light" style="font-size:0.9rem; border-radius:0; border:2px solid var(--comic-orange);">0 buku</span>
                </div>
                <div class="card-body" style="background: var(--comic-cream); max-height: 400px; overflow-y: auto;" id="books-list-body">
                    <div class="text-center py-5">
                        <div style="font-size:3rem; margin-bottom:10px;">📚</div>
                        <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:#aaa; letter-spacing:2px;">BELUM ADA BUKU</div>
                        <div style="font-size:0.85rem; color:#aaa; font-weight:700;">Scan QR buku untuk menambahkan</div>
                    </div>
                </div>
                <div class="card-footer" style="background: var(--comic-dark); border-top:3px solid var(--comic-orange);" id="submit-section">
                    <button type="button" class="btn btn-orange w-100 fw-bold py-3"
                        id="btn-submit-borrowing" disabled
                        style="border-radius:0; border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark); font-family:'Bangers',cursive; font-size:1.2rem; letter-spacing:2px;">
                        AJUKAN PEMINJAMAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendor-js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-js')
<script>
(function () {
    // ── State ──────────────────────────────────────────────────
    let currentMember = null;
    let selectedBooks = [];
    let lastScannedCode = '';
    let lastScanTime = 0;
    const SCAN_COOLDOWN_MS = 2000;
    let scannerMode = 'member'; // 'member' or 'book'

    // ── Clock ──────────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID');
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ── Scanner ────────────────────────────────────────────────
    const html5QrKiosk = new Html5Qrcode("qr-reader-kiosk");
    const qrConfig = {
        fps: 25,
        qrbox: { width: 280, height: 280 },
        aspectRatio: 1.0,
        useOptimizeCanvas: true,
        formatsToSupport: [0],
        rememberingLastUsedCamera: true,
    };

    function onScanSuccess(decodedText) {
        const now = Date.now();
        if (decodedText === lastScannedCode && now - lastScanTime < SCAN_COOLDOWN_MS) return;
        lastScannedCode = decodedText;
        lastScanTime = now;

        if (scannerMode === 'member') {
            scanMember(decodedText);
        } else {
            scanBook(decodedText);
        }
    }

    html5QrKiosk.start(
        { facingMode: "environment" },
        qrConfig,
        onScanSuccess,
        () => {}
    ).catch(() => {
        document.getElementById('qr-reader-kiosk').innerHTML = `
            <div class="alert" style="background:#fff8f0; border:3px solid var(--comic-orange); border-radius:0; padding:16px;">
                <strong style="font-family:'Fredoka One',cursive;">⚠️ Kamera tidak tersedia</strong>
                <div style="font-size:0.82rem; font-weight:700; margin-top:4px;">Gunakan input manual di bawah</div>
            </div>
        `;
    });

    // ── Manual Input ───────────────────────────────────────────
    document.getElementById('btn-kiosk-lookup').addEventListener('click', handleManualInput);
    document.getElementById('kiosk-manual-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') handleManualInput();
    });

    function handleManualInput() {
        const code = document.getElementById('kiosk-manual-input').value.trim();
        if (!code) return;
        lastScannedCode = code;
        lastScanTime = Date.now();
        if (scannerMode === 'member') {
            scanMember(code);
        } else {
            scanBook(code);
        }
    }

    // ── Scan Member ───────────────────────────────────────────
    async function scanMember(code) {
        try {
            const response = await fetch('/api/scan/member', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ member_code: code }),
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || 'Member tidak ditemukan');
            }

            currentMember = data.member;
            scannerMode = 'book';
            document.getElementById('kiosk-manual-input').dataset.codeType = 'book';
            document.getElementById('scanner-title').textContent = '📷 SCAN QR BUKU';
            document.getElementById('kiosk-error').classList.add('d-none');

            updateMemberCard();
            updateStep(2);
        } catch (error) {
            showKioskError(error.message);
        }
    }

    // ── Scan Book ─────────────────────────────────────────────
    async function scanBook(code) {
        if (!currentMember) {
            showKioskError('Scan member terlebih dahulu!');
            return;
        }

        try {
            const response = await fetch('/api/scan/book', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ member_code: currentMember.member_code, book_code: code }),
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || 'Gagal menambahkan buku');
            }

            renderBooksList(data.borrowing);
            document.getElementById('kiosk-manual-input').value = '';
            document.getElementById('kiosk-error').classList.add('d-none');
        } catch (error) {
            showKioskError(error.message);
        }
    }

    function showKioskError(msg) {
        const el = document.getElementById('kiosk-error');
        el.textContent = '⚠️ ' + msg;
        el.classList.remove('d-none');
        setTimeout(() => el.classList.add('d-none'), 4000);
    }

    // ── Update Member Card ────────────────────────────────────
    function updateMemberCard() {
        if (!currentMember) return;

        const body = document.getElementById('member-card-body');
        body.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <div style="width:70px; height:70px; border-radius:50%; border:3px solid var(--comic-orange); overflow:hidden; background:var(--comic-orange); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    ${currentMember.photo ? `<img src="${currentMember.photo}" style="width:100%;height:100%;object-fit:cover;">` : '<span style="font-size:2rem;">👤</span>'}
                </div>
                <div class="flex-grow-1">
                    <div style="font-family:'Bangers',cursive; font-size:1.8rem; color:var(--comic-dark); letter-spacing:2px;">${currentMember.name}</div>
                    <div style="font-size:0.85rem; color:#888; font-weight:700;">NIS: ${currentMember.nis_nim || '-'} | ${currentMember.class || ''} ${currentMember.major ? '| ' + currentMember.major : ''}</div>
                    <div style="font-size:0.8rem; color:#aaa; font-weight:700;">Kode: ${currentMember.member_code}</div>
                </div>
                <div class="text-center">
                    <div style="font-family:'Bangers',cursive; font-size:2rem; color:var(--comic-orange);">${currentMember.remaining_slots}</div>
                    <div style="font-size:0.7rem; color:#aaa; font-weight:900; letter-spacing:1px;">SISA SLOT</div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-outline-dark btn-sm fw-bold" onclick="resetKiosk()"
                    style="border-radius:0; border:2px solid var(--comic-dark);">
                    🔄 Reset
                </button>
            </div>
        `;
    }

    // ── Render Books List ─────────────────────────────────────
    function renderBooksList(borrowing) {
        const body = document.getElementById('books-list-body');
        const countBadge = document.getElementById('books-count-kiosk');
        const submitBtn = document.getElementById('btn-submit-borrowing');

        if (!borrowing || !borrowing.details || borrowing.details.length === 0) {
            body.innerHTML = `
                <div class="text-center py-5">
                    <div style="font-size:3rem; margin-bottom:10px;">📚</div>
                    <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:#aaa; letter-spacing:2px;">BELUM ADA BUKU</div>
                    <div style="font-size:0.85rem; color:#aaa; font-weight:700;">Scan QR buku untuk menambahkan</div>
                </div>
            `;
            countBadge.textContent = '0 buku';
            submitBtn.disabled = true;
            return;
        }

        countBadge.textContent = borrowing.details.length + ' buku';
        submitBtn.disabled = false;

        let html = '';
        borrowing.details.forEach((detail, i) => {
            const book = detail.book || {};
            html += `
            <div class="kiosk-book-card">
                <div class="book-num-kiosk">${i + 1}</div>
                <div class="kiosk-book-info flex-grow-1">
                    <strong>📕 ${book.title || '-'}</strong><br/>
                    <small>${book.book_code || ''}${book.author ? ' | ' + book.author : ''}</small>
                </div>
                <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="removeBookKiosk(${book.id})"
                    style="border-radius:0; border:2px solid var(--comic-dark); box-shadow:2px 2px 0 var(--comic-dark);">
                    ✕
                </button>
            </div>
            `;
        });

        body.innerHTML = html;
    }

    window.removeBookKiosk = async function (bookId) {
        if (!currentMember) return;
        try {
            const response = await fetch(`/api/scan/book/${bookId}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ member_code: currentMember.member_code }),
            });
            const data = await response.json();
            if (data.success) {
                renderBooksList(data.borrowing);
            }
        } catch (e) {
            showKioskError('Gagal menghapus buku');
        }
    };

    // ── Submit Borrowing ──────────────────────────────────────
    document.getElementById('btn-submit-borrowing').addEventListener('click', async function () {
        if (!currentMember || selectedBooks.length === 0) return;

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: `Peminjaman untuk ${currentMember.name} telah diajukan. Menunggu verifikasi admin.`,
            confirmButtonText: 'OK',
            confirmButtonColor: '#FF6B35',
        }).then(() => {
            resetKiosk();
        });
    });

    // ── Reset Kiosk ───────────────────────────────────────────
    window.resetKiosk = function () {
        currentMember = null;
        selectedBooks = [];
        scannerMode = 'member';
        lastScannedCode = '';

        document.getElementById('kiosk-manual-input').dataset.codeType = 'member';
        document.getElementById('kiosk-manual-input').value = '';
        document.getElementById('scanner-title').textContent = '📷 SCAN QR MEMBER';

        document.getElementById('member-card-body').innerHTML = `
            <div class="text-center py-5">
                <div style="font-size:3rem; margin-bottom:10px;">👤</div>
                <div style="font-family:'Bangers',cursive; font-size:1.2rem; color:#aaa; letter-spacing:2px;">BELUM ADA MEMBER</div>
                <div style="font-size:0.85rem; color:#aaa; font-weight:700;">Scan QR member terlebih dahulu</div>
            </div>
        `;

        renderBooksList(null);
        updateStep(1);
    };

    // ── Step Indicator ─────────────────────────────────────────
    function updateStep(step) {
        for (let i = 1; i <= 3; i++) {
            const el = document.getElementById('step-' + i);
            el.classList.remove('active', 'done');
            if (i < step) {
                el.classList.add('done');
            } else if (i === step) {
                el.classList.add('active');
            }
        }
    }

    // ── Polling: refresh member & books data ──────────────────
    setInterval(async () => {
        if (!currentMember) return;
        try {
            const response = await fetch(`/api/scan/current-member?member_code=${encodeURIComponent(currentMember.member_code)}`);
            const data = await response.json();
            if (data.success && data.pending_borrowing) {
                renderBooksList(data.pending_borrowing);
            }
        } catch (e) {}
    }, 3000);
})();
</script>
@endpush