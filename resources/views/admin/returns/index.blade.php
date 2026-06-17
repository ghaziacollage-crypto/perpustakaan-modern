@extends('layouts.app')

@section('title', 'Pengembalian Buku')
@section('page-title', 'Pengembalian Buku')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Transaksi</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Pengembalian</li>
</ul>
@endsection

@push('vendor-js')
<script src="{{ asset('vendor/qrcode/html5-qrcode.min.js') }}"></script>
@endpush

@push('custom-css')
<style>
.scan-return-body {
    display: flex;
    gap: 24px;
    min-height: 520px;
    padding: 20px;
}
.scan-return-left {
    width: 42%;
    flex-shrink: 0;
}
.scan-return-right {
    flex: 1;
    overflow-y: auto;
}
.scan-reader-box {
    border: 4px solid var(--comic-dark);
    box-shadow: 6px 6px 0 var(--comic-dark);
    background: #000;
    min-height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}
.camera-select-wrap {
    border: 3px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    padding: 12px;
    background: #fff;
    margin-bottom: 12px;
}
.scan-status {
    font-family: 'Fredoka One', cursive;
    font-size: 0.85rem;
    text-align: center;
    padding: 8px;
    border: 3px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    background: #fff;
}
.scan-manual-wrap {
    display: flex;
    gap: 10px;
    margin-top: 12px;
}
.scan-manual-wrap input {
    flex: 1;
    border: 3px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    font-weight: 800;
}
.return-form-card {
    border: 4px solid var(--comic-dark);
    box-shadow: 6px 6px 0 var(--comic-dark);
    background: #fff;
}
.return-form-empty {
    text-align: center;
    padding: 60px 30px;
    background: var(--comic-cream);
}
.return-form-empty .icon { font-size: 4rem; display: block; margin-bottom: 16px; }
.return-form-empty .title {
    font-family: 'Bangers', cursive;
    font-size: 1.5rem;
    letter-spacing: 2px;
    color: var(--comic-dark);
    margin-bottom: 8px;
}
.return-form-empty .sub {
    font-family: 'Fredoka One', cursive;
    font-size: 0.85rem;
    color: #888;
}
</style>
@endpush

@section('content')
{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4 px-4 py-3"
         style="border:3px solid var(--comic-dark); border-radius:0; font-family:'Fredoka One',cursive; font-size:0.9rem; background:var(--comic-cream); color:var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark);">
        <span style="font-size:1.2rem;">✅</span> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 px-4 py-3"
         style="border:3px solid var(--comic-red); border-radius:0; font-family:'Fredoka One',cursive; font-size:0.9rem; background:#fff; color:var(--comic-red); box-shadow:4px 4px 0 var(--comic-red);">
        <span style="font-size:1.2rem;">❌</span> {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">📥 PENGEMBALIAN BUKU</span>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.returns.scan') }}" class="btn btn-outline-dark fw-bold"
               style="border-radius:0; border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); font-family:'Fredoka One',cursive; font-size:0.8rem;">
                🔍 Full Screen Scan
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="scan-return-body">

            {{-- LEFT: QR Scanner --}}
            <div class="scan-return-left">
                {{-- Camera select --}}
                <div class="camera-select-wrap">
                    <label style="font-family:'Fredoka One',cursive; font-size:0.7rem; letter-spacing:2px; font-weight:900; color:var(--comic-dark); display:block; margin-bottom:6px;">
                        📷 PILIH KAMERA
                    </label>
                    <select id="camera-select" class="form-select" style="border-radius:0; border:2px solid var(--comic-dark); font-weight:800;">
                        <option value="">-- Memuat...</option>
                    </select>
                </div>

                {{-- QR Reader --}}
                <div id="qr-reader" class="scan-reader-box"></div>

                {{-- Scan Status --}}
                <div id="scanStatus" class="scan-status" style="color:#888;">
                    Arahkan kamera ke QR code return buku
                </div>

                {{-- Manual Input --}}
                <div class="scan-manual-wrap">
                    <input type="text" id="manual-code" class="form-control"
                           placeholder="Atau input kode transaksi..." style="border-radius:0;">
                    <button type="button" id="btn-manual" class="btn btn-comic"
                            style="border-radius:0; border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); font-family:'Fredoka One',cursive; white-space:nowrap;">
                        🔍 CARI
                    </button>
                </div>

                {{-- Tombol Daftar Peminjaman --}}
                <a href="{{ route('admin.borrowings.index') }}" class="btn w-100 mt-3 fw-bold"
                   style="background:var(--comic-cream); color:var(--comic-dark); border-radius:0; border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); font-family:'Fredoka One',cursive; font-size:0.82rem; letter-spacing:1px; padding:10px; display:flex; align-items:center; justify-content:center; gap:6px;">
                    <i class="ki-duotone ki-book fs-4"></i>
                    📋 DAFTAR PEMINJAMAN
                </a>
            </div>

            {{-- RIGHT: Return Form --}}
            <div class="scan-return-right">
                <div class="return-form-card" id="formCard">
                    <div class="card-header" style="background:var(--comic-dark); border-bottom:3px solid var(--comic-orange);">
                        <div style="font-family:'Bangers',cursive; font-size:1.1rem; color:var(--comic-orange); letter-spacing:2px;">
                            📋 DATA PEMINJAMAN
                        </div>
                    </div>

                    {{-- Empty state --}}
                    <div class="return-form-empty" id="formEmpty">
                        <span class="icon">📷</span>
                        <div class="title">SCAN QR TERLEBIH DAHULU</div>
                        <div class="sub">Arahkan kamera ke QR code return buku</div>
                    </div>

                    {{-- Form data (shown after scan) --}}
                    <div class="card-body" id="formData" style="background:var(--comic-cream);" class="d-none">
                        <div id="formContent">
                            {{-- Header info --}}
                            <div style="background:#fff; border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark); padding:14px 18px; margin-bottom:20px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div id="memberAvatar" style="width:44px; height:44px; border:2px solid var(--comic-dark); display:flex; align-items:center; justify-content:center; font-family:'Bangers',cursive; font-size:1.2rem; background:var(--comic-cream); flex-shrink:0;">
                                        👤
                                    </div>
                                    <div>
                                        <div style="font-family:'Bangers',cursive; font-size:1rem; letter-spacing:1px; color:var(--comic-dark);" id="trxCode">—</div>
                                        <div style="font-family:'Fredoka One',cursive; font-size:0.8rem; color:#888;" id="memberName">—</div>
                                    </div>
                                    <div style="margin-left:auto; text-align:right;">
                                        <div style="font-family:'Fredoka One',cursive; font-size:0.7rem; color:#888; letter-spacing:1px;">JATUH TEMPO</div>
                                        <div style="font-family:'Bangers',cursive; font-size:0.95rem; color:var(--comic-dark);" id="dueDate">—</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Books list --}}
                            <div style="margin-bottom:20px;">
                                <div style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:2px; text-transform:uppercase; color:var(--comic-dark); font-weight:900; margin-bottom:10px;">
                                    📕 BUKU YANG DIKEMBALIKAN
                                </div>
                                <div id="booksList"></div>
                                <div id="detailIdsContainer" class="d-none"></div>
                            </div>

                            {{-- Return Form --}}
                            <form method="POST" id="returnForm">
                                @csrf
                                <input type="hidden" name="detail_ids" id="detailIdsInput" value="">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label style="font-family:'Fredoka One',cursive; font-size:0.7rem; letter-spacing:2px; text-transform:uppercase; color:var(--comic-dark); font-weight:900; display:block; margin-bottom:4px;">
                                            📅 TANGGAL KEMBALI
                                        </label>
                                        <input type="date" name="return_date" id="returnDate" class="form-control"
                                               style="border:3px solid var(--comic-dark); border-radius:0; font-weight:800;" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-family:'Fredoka One',cursive; font-size:0.7rem; letter-spacing:2px; text-transform:uppercase; color:var(--comic-dark); font-weight:900; display:block; margin-bottom:4px;">
                                            📋 KONDISI
                                        </label>
                                        <select name="condition" id="conditionSelect" class="form-select" required
                                                style="border:3px solid var(--comic-dark); border-radius:0; font-weight:800;">
                                            <option value="Baik">✅ Baik — Normal</option>
                                            <option value="Rusak Ringan">⚠️ Rusak Ringan</option>
                                            <option value="Rusak Berat">❌ Rusak Berat</option>
                                            <option value="Hilang">🚫 Buku Hilang</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label style="font-family:'Fredoka One',cursive; font-size:0.7rem; letter-spacing:2px; text-transform:uppercase; color:var(--comic-dark); font-weight:900; display:block; margin-bottom:4px;">
                                            📝 CATATAN
                                        </label>
                                        <textarea name="notes" id="notesInput" class="form-control" rows="2"
                                                  style="border:3px solid var(--comic-dark); border-radius:0;"
                                                  placeholder="Opsional..."></textarea>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-comic w-100"
                                            style="border-radius:0; border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark); font-family:'Fredoka One',cursive; font-size:0.9rem; padding:12px;">
                                        <i class="ki-duotone ki-check fs-5" style="color:#fff;"></i>
                                        SIMPAN PENGEMBALIAN
                                    </button>
                                    <button type="button" class="btn btn-outline-dark w-100 mt-2"
                                            id="btnReset"
                                            style="border-radius:0; border:3px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark); font-family:'Fredoka One',cursive;">
                                        🔄 Scan Baru
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-js')
<script>
(function () {
    let scanner = null;
    let lastScanned = '';
    let lastScanTime = 0;
    const COOLDOWN = 2000;
    let currentBorrowingId = null;

    // ── Init Scanner ──
    async function initScanner() {
        const select = document.getElementById('camera-select');
        const reader = document.getElementById('qr-reader');

        if (typeof Html5Qrcode === 'undefined') {
            reader.innerHTML = '<div style="color:#fff; text-align:center; padding:20px; font-family:\'Fredoka One\',cursive;">❌ Library QR tidak tersedia. Gunakan input manual.</div>';
            select.innerHTML = '<option value="">❌ Library tidak tersedia</option>';
            return;
        }

        let devices = [];
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            stream.getTracks().forEach(t => t.stop());
            devices = await Html5Qrcode.getCameras();
        } catch (e) {
            reader.innerHTML = '<div style="color:#fff; text-align:center; padding:20px; font-family:\'Fredoka One\',cursive;">🔒 Izin kamera ditolak.<br>Gunakan input manual.</div>';
            select.innerHTML = '<option value="">❌ Kamera tidak tersedia</option>';
            return;
        }

        if (!devices || devices.length === 0) {
            reader.innerHTML = '<div style="color:#fff; text-align:center; padding:20px; font-family:\'Fredoka One\',cursive;">📷 Kamera tidak ditemukan.<br>Gunakan input manual.</div>';
            select.innerHTML = '<option value="">❌ Tidak ada kamera</option>';
            return;
        }

        select.innerHTML = devices.map(function (d, i) {
            return '<option value="' + d.id + '">' + (d.label || ('Kamera ' + (i + 1))) + '</option>';
        }).join('');

        scanner = new Html5Qrcode('qr-reader');

        async function startCam(id) {
            if (scanner) { try { await scanner.stop(); } catch (_) {} }
            try {
                await scanner.start(id, { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    handleScan, () => {});
            } catch (e) {
                setStatus('❌ Gagal memulai kamera: ' + (e.message || e), 'var(--comic-red)');
            }
        }

        select.addEventListener('change', () => { if (select.value) startCam(select.value); });

        await startCam(devices[0].id);
        select.value = devices[0].id;
    }

    // ── Handle QR Scan ──
    function handleScan(decodedText) {
        console.log('🔍 RAW SCAN:', decodedText); // DEBUG

        const now = Date.now();
        if (decodedText === lastScanned && now - lastScanTime < COOLDOWN) return;
        lastScanned = decodedText;
        lastScanTime = now;

        if (!decodedText.startsWith('RET-')) {
            console.log('❌ Bukan format RET-'); // DEBUG
            setStatus('⚠️ Bukan QR Return. Coba lagi.', 'var(--comic-red)');
            return;
        }

        const code = decodedText.replace('RET-', '');
        console.log('✅ Transaction Code:', code); // DEBUG
        const statusText = '📷 QR Terdeteksi: ' + decodedText;
        setStatus(statusText, 'var(--comic-orange)');
        fetchBorrowing(code);
    }

    // ── Fetch Borrowing Data ──
    async function fetchBorrowing(code) {
        const url = '/admin/borrowings/lookup-by-code?code=' + encodeURIComponent(code);
        console.log('🌐 Fetching:', url);

        try {
            const res = await fetch(url);
            console.log('📡 HTTP Status:', res.status);
            console.log('📡 Content-Type:', res.headers.get('content-type'));

            // Check if response is HTML (error page) instead of JSON
            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await res.text();
                console.error('❌ Response is not JSON (HTML error page):', text.substring(0, 200));
                setStatus('❌ Server error: bukan JSON response', 'var(--comic-red)');
                alert('Server Error!\nHTTP Status: ' + res.status + '\n\nBuka console (F12) untuk detail.');
                return;
            }

            const json = await res.json();
            console.log('📦 Response JSON:', json);

            if (!json.success) {
                setStatus('❌ ' + (json.error || 'Tidak ditemukan'), 'var(--comic-red)');
                alert('Gagal mengambil data:\n' + (json.error || 'Tidak ditemukan') + '\n\nKode: ' + code);
                return;
            }

            fillFormData(json.data);
            showForm();
            setStatus('✅ Data dimuat: ' + json.data.transaction_code, 'var(--comic-green)');
        } catch (e) {
            console.error('❌ Fetch error:', e);
            setStatus('❌ Gagal mengambil data: ' + e.message, 'var(--comic-red)');
            alert('Fetch Error: ' + e.message);
        }
    }

    // ── Fill Form with Data ──
    function fillFormData(data) {
        currentBorrowingId = data.id;

        document.getElementById('trxCode').textContent = data.transaction_code;
        document.getElementById('memberName').textContent = data.member.name + ' — ' + (data.member.nis_nim || '-');
        document.getElementById('dueDate').textContent = data.due_date;
        document.getElementById('dueDate').style.color = data.is_overdue ? 'var(--comic-red)' : 'var(--comic-dark)';

        // Member avatar initial
        document.getElementById('memberAvatar').textContent = data.member.name.charAt(0).toUpperCase();

        // Books list
        const unreturned = data.books.filter(b => b.status === 'borrowed');
        document.getElementById('booksList').innerHTML = unreturned.length
            ? unreturned.map(b => '<div style="display:flex; align-items:center; gap:10px; padding:8px 12px; border:2px solid var(--comic-dark); background:#fff; box-shadow:2px 2px 0 var(--comic-dark); margin-bottom:8px;"><input type="checkbox" class="form-check-input book-check" value="' + b.id + '" style="border:2px solid var(--comic-dark); border-radius:0; width:18px; height:18px;" checked><div><div style="font-family:Fredoka One,cursive; font-size:0.85rem; color:var(--comic-dark);">📕 ' + b.title + '</div><div style="font-size:0.7rem; color:#888; font-weight:700;">' + b.book_code + '</div></div></div>').join('')
            : '<div style="font-family:Fredoka One,cursive; font-size:0.85rem; color:#888;">Semua buku sudah dikembalikan.</div>';

        // Set detail_ids hidden input
        document.getElementById('detailIdsInput').value = unreturned.map(b => b.id).join(',');

        // Pre-fill return date
        document.getElementById('returnDate').value = new Date().toISOString().split('T')[0];

        // Update checkbox select-all
        updateSelectAll();
    }

    // ── Update Select All ──
    function updateSelectAll() {
        const booksList = document.getElementById('booksList');
        if (!booksList) return;

        const allChecks = booksList.querySelectorAll('.book-check');
        const checkedCount = booksList.querySelectorAll('.book-check:checked').length;
        const hasBooks = allChecks.length > 0;
        const allChecked = hasBooks && checkedCount === allChecks.length;

        // Add select all header if not exists
        if (!booksList.querySelector('#selectAllBooks')) {
            const headerDiv = document.createElement('div');
            headerDiv.style.cssText = 'margin-bottom:8px; display:flex; align-items:center; gap:8px;';
            headerDiv.innerHTML = '<input type="checkbox" id="selectAllBooks" style="border:2px solid var(--comic-dark); border-radius:0; width:18px; height:18px;">' +
                '<label for="selectAllBooks" style="font-family:Fredoka One,cursive; font-size:0.8rem; font-weight:900; cursor:pointer;">PILIH SEMUA</label>';
            booksList.insertBefore(headerDiv, booksList.firstChild);

            document.getElementById('selectAllBooks').addEventListener('change', function () {
                booksList.querySelectorAll('.book-check:not(:disabled)').forEach(cb => cb.checked = this.checked);
                syncDetailIds();
            });
        }

        // Sync select all checkbox state
        const selectAllCheck = document.getElementById('selectAllBooks');
        if (selectAllCheck) selectAllCheck.checked = allChecked;

        // Attach change listeners to book checkboxes
        booksList.querySelectorAll('.book-check').forEach(cb => {
            if (!cb.dataset.listener) {
                cb.dataset.listener = '1';
                cb.addEventListener('change', function () {
                    syncDetailIds();
                    updateSelectAll();
                });
            }
        });

        syncDetailIds();
    }

    // ── Sync detail_ids ──
    function syncDetailIds() {
        const ids = Array.from(document.querySelectorAll('.book-check:checked:not(:disabled)')).map(cb => cb.value);
        document.getElementById('detailIdsInput').value = ids.join(',');
    }

    // ── Show Form ──
    function showForm() {
        document.getElementById('formEmpty').style.display = 'none';
        document.getElementById('formData').classList.remove('d-none');
        document.getElementById('formData').style.display = 'block';
    }

    // ── Reset Form ──
    function resetForm() {
        currentBorrowingId = null;
        lastScanned = '';
        document.getElementById('formEmpty').style.display = 'block';
        document.getElementById('formData').classList.add('d-none');
        document.getElementById('formData').style.display = 'none';
        setStatus('Arahkan kamera ke QR code return buku', '#888');
    }

    // ── Set Status Text ──
    function setStatus(msg, color) {
        const el = document.getElementById('scanStatus');
        el.textContent = msg;
        el.style.color = color;
    }

    // ── Manual Search ──
    document.getElementById('btn-manual').addEventListener('click', function () {
        const code = document.getElementById('manual-code').value.trim();
        if (!code) return;
        // Try as-is first (if user pasted RET-...)
        const cleanCode = code.startsWith('RET-') ? code.replace('RET-', '') : code;
        fetchBorrowing(cleanCode);
    });

    document.getElementById('manual-code').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btn-manual').click();
        }
    });

    // ── Reset Button ──
    document.getElementById('btnReset').addEventListener('click', resetForm);

    // ── Form Submit ──
    document.getElementById('returnForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const ids = Array.from(document.querySelectorAll('.book-check:checked:not(:disabled)')).map(cb => cb.value);
        document.getElementById('detailIdsInput').value = ids.join(',');

        if (ids.length === 0) {
            alert('Pilih minimal 1 buku untuk dikembalikan.');
            return;
        }

        const bookCount = ids.length;
        const confirmed = confirm('Yakin ingin mengembalikan ' + bookCount + ' buku?\n\nTekan OK untuk simpan, Cancel untuk batal.');
        if (!confirmed) {
            return;
        }

        // Set action URL before normal form submit
        this.action = '/admin/returns/' + currentBorrowingId;

        // Submit normally (not AJAX) so controller redirect + flash message works
        this.submit();
    });

    // ── Start ──
    initScanner();
})();
</script>
@endpush
