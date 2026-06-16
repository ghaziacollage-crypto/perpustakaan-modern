@extends('landing.layout')

@section('title', 'Member Area — ' . app_setting('app_name', 'Perpustakaan'))
@section('page-title', 'Member Area')

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            @include('landing.partials.brand-logo')
        </a>
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navMember">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMember">
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
                    <a class="nav-link active-link btn btn-dark btn-sm px-3 fw-bold" href="{{ route('member.index') }}">👤 Member</a>
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

{{-- Hero Section --}}
<div class="detail-hero" style="min-height: 30vh;">
    <div class="container">
        <div class="text-center text-white">
            <div class="section-label" style="color: var(--comic-yellow);">MEMBER AREA</div>
            <h1 class="comic-section-title text-white mb-2">👤 AREA <span class="text-orange">MEMBER</span></h1>
            <p class="text-white-50 fw-bold">Lihat profil dan peminjaman Anda dengan scan QR member card</p>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="container py-5">
    {{-- Scan Section --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card scan-card" style="border: 4px solid var(--comic-dark); box-shadow: 6px 6px 0 var(--comic-dark); border-radius: 0;">
                <div class="card-header" style="background: var(--comic-dark); border-bottom: 4px solid var(--comic-orange);">
                    <div class="card-title" style="font-family: 'Bangers', cursive; font-size: 1.5rem; letter-spacing: 2px; color: var(--comic-orange);">
                        📷 SCAN QR MEMBER ANDA
                    </div>
                </div>
                <div class="card-body" style="background: var(--comic-cream);">
                    {{-- Camera selection --}}
                    <div class="mb-3" style="max-width: 400px; margin: 0 auto;">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                            📷 PILIH KAMERA
                        </label>
                        <select id="camera-select-member" class="form-select" style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 800;">
                            <option value="">-- Memuat kamera... --</option>
                        </select>
                    </div>

                    {{-- Scanner container --}}
                    <div id="qr-reader-member" style="width: 100%; max-width: 400px; margin: 0 auto; border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark);"></div>

                    <div class="text-center mt-4 mb-3">
                        <div style="font-family: 'Fredoka One', cursive; color: #aaa; letter-spacing: 2px; font-size: 0.8rem;">— ATAU INPUT MANUAL —</div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <input type="text" id="manual-member-code" class="form-control form-control-lg"
                            placeholder="Ketik kode member..." style="font-weight: 800; border: 3px solid var(--comic-dark); border-radius: 0; max-width: 300px;"
                            data-comic-input>
                        <button type="button" class="btn btn-orange btn-lg fw-bold" id="btn-manual-lookup"
                            style="border-radius: 0; border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark);">
                            🔍 CEK
                        </button>
                    </div>
                    <div id="manual-error-member" class="text-center mt-2 d-none" style="font-weight: 700; color: var(--comic-red);"></div>
                </div>
            </div>

            {{-- Register Link --}}
            <div class="text-center mt-4">
                <p style="font-family: 'Fredoka One', cursive; color: #aaa; font-size: 0.9rem; letter-spacing: 1px;">
                    Belum punya akun?
                    <a href="{{ route('member.register') }}"
                       style="color: var(--comic-orange); text-decoration: underline; font-weight: 900;">
                        📝 Daftar di sini
                    </a>
                </p>
            </div>
        </div>
    </div>

    {{-- Member Info Section (shown after scan) --}}
    <div id="member-info-section" class="row mt-5 d-none">
        <div class="col-12">
            <div class="card" style="border: 4px solid var(--comic-dark); box-shadow: 6px 6px 0 var(--comic-dark); border-radius: 0;">
                <div class="card-header" style="background: var(--comic-dark);">
                    <div class="d-flex align-items-center gap-3">
                        <div id="member-photo-container" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid var(--comic-orange); overflow: hidden; background: var(--comic-orange); display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 1.5rem;">👤</span>
                        </div>
                        <div>
                            <div id="member-name" style="font-family: 'Bangers', cursive; font-size: 1.5rem; color: var(--comic-orange); letter-spacing: 2px;">—</div>
                            <div id="member-meta" style="font-size: 0.8rem; color: rgba(255,255,255,0.7); font-weight: 700;">—</div>
                        </div>
                        <div class="ms-auto">
                            <span id="member-status-badge" class="badge" style="font-size: 0.9rem; border-radius: 0; border: 2px solid; padding: 6px 16px;">—</span>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="background: var(--comic-cream);">
                    {{-- Stats --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div style="background: #fff; border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark); padding: 20px; text-align: center;">
                                <div style="font-family: 'Bangers', cursive; font-size: 2.5rem; color: var(--comic-orange);" id="stat-slots">—</div>
                                <div style="font-size: 0.75rem; font-weight: 900; color: #aaa; letter-spacing: 2px;">SISA SLOT</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background: #fff; border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark); padding: 20px; text-align: center;">
                                <div style="font-family: 'Bangers', cursive; font-size: 2.5rem; color: var(--comic-blue);" id="stat-borrowing">—</div>
                                <div style="font-size: 0.75rem; font-weight: 900; color: #aaa; letter-spacing: 2px;">SEDANG PINJAM</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background: #fff; border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark); padding: 20px; text-align: center;">
                                <div style="font-family: 'Bangers', cursive; font-size: 2.5rem; color: var(--comic-red);" id="stat-overdue">—</div>
                                <div style="font-size: 0.75rem; font-weight: 900; color: #aaa; letter-spacing: 2px;">TERLAMBAT</div>
                            </div>
                        </div>
                    </div>

                    {{-- Borrowings List --}}
                    <div id="borrowings-list">
                        {{-- Dynamic content --}}
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-outline-dark fw-bold px-4" onclick="resetMemberView()"
                            style="border-radius: 0; border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark);">
                            🔄 Scan Member Lain
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="row mt-4">
        <div class="col-lg-8 mx-auto">
            <div class="speech-bubble" style="background: rgba(255,248,240,0.95);">
                <div style="font-family: 'Bangers', cursive; font-size: 1.2rem; color: var(--comic-dark); letter-spacing: 1px; margin-bottom: 10px;">
                    💡 Cara Menggunakan
                </div>
                <ol style="font-weight: 700; color: var(--comic-dark); line-height: 2;">
                    <li>Arahkan kamera ke QR code di kartu member Anda</li>
                    <li>Atau ketik kode member secara manual</li>
                    <li>Lihat profil dan daftar peminjaman Anda</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
<footer class="comic-footer py-4 mt-5">
    <div class="container">
        <div class="text-center text-light">
            <div style="font-family: 'Fredoka One', cursive; color: var(--comic-orange); letter-spacing: 2px;">
                📚 {{ app_setting('app_name', 'Perpustakaan Modern') }}
            </div>
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-top: 5px;">
                {{ now()->format('Y') }} — All Rights Reserved
            </div>
        </div>
    </div>
</footer>
@endsection

@push('vendor-js')
<script src="{{ asset('vendor/qrcode/html5-qrcode.min.js') }}"></script>
@endpush

@push('custom-js')
<script>
(function () {
    let memberScanner = null;
    let lastScannedCode = '';
    let lastScanTime = 0;
    const SCAN_COOLDOWN_MS = 2000;

    // Populate camera dropdown and start scanner
    async function populateCameraSelect() {
        const el = document.getElementById('qr-reader-member');
        const select = document.getElementById('camera-select-member');
        if (!el || !select) return;

        // Check if Html5Qrcode library is loaded
        if (typeof Html5Qrcode === 'undefined') {
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-red); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">❌</div>
                    <strong style="color:var(--comic-dark);">Library QR Scanner Gagal Dimuat</strong>
                    <div style="color:#666; margin-top:8px; font-size:0.78rem; font-weight:700;">
                        Browser memblokir akses file JS.<br>Gunakan <strong>input manual</strong> di bawah.<br><br>
                        <span style="font-size:0.72rem; color:#888;">
                        💡 Matikan Tracking Prevention / AdBlock, lalu refresh.
                        </span>
                    </div>
                </div>`;
            select.innerHTML = '<option value="">❌ Library tidak tersedia</option>';
            return;
        }

        let devices = [];
        try {
            // Request camera permission first
            const testStream = await navigator.mediaDevices.getUserMedia({ video: true });
            testStream.getTracks().forEach(t => t.stop());
            devices = await Html5Qrcode.getCameras();
        } catch (permErr) {
            // Camera permission denied
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">🔒</div>
                    <strong style="color:var(--comic-dark);">Izin Kamera Ditolak</strong>
                    <div style="color:#666; margin-top:8px; font-size:0.78rem; font-weight:700;">
                        Izin akses kamera ditolak oleh browser.<br>
                        Gunakan <strong>input manual</strong> di bawah.<br><br>
                        <span style="font-size:0.72rem; color:#888;">
                        💡 Tekan 🔒 di address bar → Allow → Refresh halaman
                        </span>
                    </div>
                </div>`;
            select.innerHTML = '<option value="">❌ Kamera tidak tersedia</option>';
            return;
        }

        if (!devices || devices.length === 0) {
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">📷</div>
                    <strong style="color:var(--comic-dark);">Kamera Tidak Ditemukan</strong>
                    <div style="color:#666; margin-top:8px; font-size:0.78rem; font-weight:700;">
                        Pastikan webcam terhubung.<br>Gunakan <strong>input manual</strong> di bawah.
                    </div>
                </div>`;
            select.innerHTML = '<option value="">❌ Tidak ada kamera</option>';
            return;
        }

        // Populate dropdown
        select.innerHTML = devices.map((device, idx) =>
            `<option value="${device.id}">${device.label || `Kamera ${idx + 1}`}</option>`
        ).join('');

        // Auto-start first camera
        await initMemberScanner(devices[0].id);
    }

    // Initialize scanner with specific camera ID
    async function initMemberScanner(cameraId) {
        const el = document.getElementById('qr-reader-member');
        if (!el) return;

        try {
            if (memberScanner) {
                await memberScanner.stop();
                memberScanner = null;
            }

            memberScanner = new Html5Qrcode('qr-reader-member');

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
            };

            await memberScanner.start(
                cameraId,
                config,
                handleMemberScan,
                () => {} // Ignore per-frame errors
            );
        } catch (startErr) {
            el.innerHTML = `
                <div style="padding:20px 16px; background:#fff8f0; border:3px solid var(--comic-orange); font-family:'Fredoka One', cursive; font-size:0.85rem; text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">📷</div>
                    <strong style="color:var(--comic-dark);">Gagal Memulai Kamera</strong>
                    <div style="color:#666; margin-top:6px; font-size:0.78rem; font-weight:700;">
                        Error: ${startErr?.message || startErr || 'Tidak dapat memulai scanner'}
                    </div>
                    <div style="margin-top:8px; font-size:0.72rem; color:#888;">
                        Gunakan <strong>input manual</strong> sebagai alternatif
                    </div>
                </div>`;
        }
    }

    // Handle QR scan success
    function handleMemberScan(decodedText) {
        const now = Date.now();
        if (decodedText === lastScannedCode && now - lastScanTime < SCAN_COOLDOWN_MS) return;
        lastScannedCode = decodedText;
        lastScanTime = now;

        lookupMember(decodedText);
    }

    // Show loading animation then redirect to dashboard
    function showAuthAnimation(code) {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;z-index:99999;background:rgba(26,26,46,0.97);display:flex;flex-direction:column;align-items:center;justify-content:center;';
            overlay.innerHTML = `
                <div style="text-align:center;">
                    <div style="font-size:3rem; margin-bottom:15px;">📚</div>
                    <div id="authStep" style="font-family:'Fredoka One',cursive; font-size:1.1rem; color:var(--comic-orange); letter-spacing:2px; margin-bottom:20px;">
                        Memverifikasi...
                    </div>
                    <div style="width:50px; height:50px; border:4px solid var(--comic-orange); border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto;"></div>
                </div>
                <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
            `;
            document.body.appendChild(overlay);

            const steps = [
                { text: 'Memverifikasi...', delay: 600 },
                { text: 'Berhasil!', delay: 400 },
            ];

            let elapsed = 0;
            steps.forEach((step, idx) => {
                elapsed += step.delay;
                setTimeout(() => {
                    const stepEl = document.getElementById('authStep');
                    if (stepEl) {
                        stepEl.textContent = step.text;
                        if (idx === steps.length - 1) {
                            stepEl.style.color = '#27ae60';
                        }
                    }
                    if (idx === steps.length - 1) {
                        setTimeout(() => {
                            overlay.remove();
                            window.location.href = '/member/dashboard?code=' + encodeURIComponent(code);
                        }, 300);
                    }
                }, elapsed);
            });
        });
    }

    // Camera selection change
    document.getElementById('camera-select-member').addEventListener('change', async function () {
        const cameraId = this.value;
        if (cameraId) {
            await initMemberScanner(cameraId);
        }
    });

    // Manual lookup
    document.getElementById('btn-manual-lookup').addEventListener('click', function () {
        const code = document.getElementById('manual-member-code').value.trim();
        if (!code) return;
        lookupMember(code);
    });

    document.getElementById('manual-member-code').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (code) lookupMember(code);
        }
    });

    async function lookupMember(code) {
        try {
            const response = await fetch(`/member/lookup?code=${encodeURIComponent(code)}`);
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || 'Member tidak ditemukan');
            }

            // Show loading animation then redirect to dashboard
            await showAuthAnimation(code);
        } catch (error) {
            document.getElementById('manual-error-member').textContent = '⚠️ ' + error.message;
            document.getElementById('manual-error-member').classList.remove('d-none');
        }
    }

    function renderMemberInfo(data) {
        const member = data.member;
        const borrowings = data.borrowings || [];

        document.getElementById('member-info-section').classList.remove('d-none');

        document.getElementById('member-name').textContent = member.name;
        document.getElementById('member-meta').textContent = `NIS: ${member.nis_nim || '-'} | ${member.class || '-'}`;

        if (member.photo) {
            document.getElementById('member-photo-container').innerHTML =
                `<img src="${member.photo}" alt="${member.name}" style="width:100%;height:100%;object-fit:cover;">`;
        }

        const statusBadge = document.getElementById('member-status-badge');
        if (member.is_active) {
            statusBadge.textContent = '🟢 AKTIF';
            statusBadge.style.cssText = 'background: var(--comic-blue); color: #fff;';
        } else {
            statusBadge.textContent = '🔴 TIDAK AKTIF';
            statusBadge.style.cssText = 'background: var(--comic-red); color: #fff;';
        }

        const overdueCount = borrowings.filter(b => b.is_overdue && b.status !== 'returned' && b.status !== 'pending').length;
        document.getElementById('stat-slots').textContent = member.remaining_slots;
        document.getElementById('stat-borrowing').textContent = member.active_borrowings_count;
        document.getElementById('stat-overdue').textContent = overdueCount;

        const borrowingsList = document.getElementById('borrowings-list');
        if (borrowings.length === 0) {
            borrowingsList.innerHTML = `
                <div class="text-center py-4" style="font-family: 'Bangers', cursive; font-size: 1.2rem; color: #aaa; letter-spacing: 2px;">
                    📚 BELUM ADA PEMINJAMAN
                </div>
            `;
        } else {
            let html = '<div style="font-family: \'Fredoka One\', cursive; font-size: 0.9rem; color: var(--comic-dark); margin-bottom: 12px; letter-spacing: 2px;">📋 RIWAYAT PEMINJAMAN</div>';
            html += '<div class="table-responsive"><table class="table table-bordered" style="border: 2px solid var(--comic-dark);">';
            html += '<thead style="background: var(--comic-dark); color: #fff;"><tr>';
            html += '<th style="font-family:\'Fredoka One\',cursive; letter-spacing:1px;">KODE</th>';
            html += '<th style="font-family:\'Fredoka One\',cursive; letter-spacing:1px;">STATUS</th>';
            html += '<th style="font-family:\'Fredoka One\',cursive; letter-spacing:1px;">JATUH TEMPO</th>';
            html += '<th style="font-family:\'Fredoka One\',cursive; letter-spacing:1px;">BUKU</th>';
            html += '</tr></thead><tbody>';

            borrowings.forEach(b => {
                const statusClass = b.status === 'pending' ? 'background: var(--comic-yellow); color: var(--comic-dark);'
                    : b.status === 'late' || b.is_overdue ? 'background: var(--comic-red); color: #fff;'
                    : 'background: var(--comic-blue); color: #fff;';

                html += `<tr style="border:2px solid var(--comic-dark);">
                    <td style="font-weight:800;">${b.transaction_code}</td>
                    <td><span class="badge" style="${statusClass}">${b.status_label}</span></td>
                    <td style="font-weight:700;">${b.due_date}</td>
                    <td>${b.books.length} buku</td>
                </tr>`;
            });

            html += '</tbody></table></div>';
            borrowingsList.innerHTML = html;
        }

        document.getElementById('member-info-section').scrollIntoView({ behavior: 'smooth' });
    }

    window.resetMemberView = function () {
        document.getElementById('member-info-section').classList.add('d-none');
        document.getElementById('manual-member-code').value = '';
        lastScannedCode = '';
    };

    // Start scanner on DOM ready
    document.addEventListener('DOMContentLoaded', async () => {
        await populateCameraSelect();
    });
})();
</script>
@endpush
