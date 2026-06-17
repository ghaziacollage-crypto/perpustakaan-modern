@extends('layouts.app')

@section('title', 'Scan Return QR — Admin')

@push('vendor-js')
<script src="{{ asset('vendor/qrcode/html5-qrcode.min.js') }}"></script>
@endpush

@push('custom-css')
<style>
body { overflow: hidden; }
.scan-return-body {
    height: calc(100vh - 65px);
    display: flex;
    overflow: hidden;
}
.scan-return-left {
    width: 45%;
    border-right: 4px solid var(--comic-dark);
    display: flex;
    flex-direction: column;
    background: var(--comic-cream);
}
.scan-return-right {
    width: 55%;
    overflow-y: auto;
    background: var(--comic-cream);
}
.scan-header {
    background: var(--comic-dark);
    border-bottom: 4px solid var(--comic-orange);
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.return-form-card {
    border: 4px solid var(--comic-dark);
    box-shadow: 5px 5px 0 var(--comic-dark);
    background: #fff;
    margin: 20px;
}
</style>
@endpush

@section('content')
{{-- Header --}}
<div class="scan-header">
    <div style="font-family:'Bangers',cursive; color:var(--comic-orange); letter-spacing:3px; font-size:1.2rem;">
        📤 SCAN RETURN BUKU
    </div>
    <div class="d-flex gap-2 align-items-center">
        <select id="camera-select" class="form-select form-select-sm" style="border-radius:0; border:2px solid var(--comic-dark); max-width:200px; font-weight:800;">
            <option value="">-- Pilih Kamera --</option>
        </select>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-outline-light fw-bold" style="border-radius:0; border:2px solid rgba(255,255,255,0.5);">
            ← Kembali
        </a>
    </div>
</div>

{{-- Body --}}
<div class="scan-return-body">
    {{-- LEFT: Scanner --}}
    <div class="scan-return-left">
        <div style="flex:1; display:flex; flex-direction:column; padding:15px;">
            <div id="qr-reader" style="flex:1; border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark); min-height:300px;"></div>
            <div id="scanStatus" class="mt-2 text-center" style="font-family:'Fredoka One',cursive; font-size:0.85rem; color:#888;"></div>
        </div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="scan-return-right">
        <div class="return-form-card" id="formCard" style="opacity:0.4; pointer-events:none;">
            <div class="card-header" style="background:var(--comic-dark); border-bottom:4px solid var(--comic-orange);">
                <div style="font-family:'Bangers',cursive; color:var(--comic-orange); letter-spacing:2px; font-size:1.1rem;">
                    📋 DATA PEMINJAMAN
                </div>
            </div>
            <div class="card-body" style="background:var(--comic-cream);" id="formBody">
                <div class="text-center text-muted py-5" id="formEmpty">
                    <div style="font-size:3rem; margin-bottom:10px;">📷</div>
                    <div style="font-family:'Fredoka One',cursive; color:var(--comic-dark); letter-spacing:2px;">
                        SCAN QR CODE TERLEBIH DAHULU
                    </div>
                    <div style="font-size:0.82rem; color:#888; font-weight:700; margin-top:5px;">
                        Arahkan kamera ke QR code return buku
                    </div>
                </div>

                {{-- Data will be filled here after scan --}}
                <div id="formData" class="d-none">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width:50px; height:50px; background:var(--comic-cream); border:3px solid var(--comic-dark); display:flex; align-items:center; justify-content:center; font-family:'Bangers',cursive; font-size:1.5rem; color:var(--comic-dark);">
                            📤
                        </div>
                        <div>
                            <div style="font-family:'Fredoka One',cursive; font-size:1.1rem; color:var(--comic-dark);" id="trxCode">—</div>
                            <div style="font-size:0.8rem; color:#888; font-weight:700;" id="memberName">—</div>
                        </div>
                    </div>

                    {{-- Books --}}
                    <div class="mb-4">
                        <div style="font-family:'Fredoka One',cursive; font-size:0.8rem; color:#888; letter-spacing:2px; margin-bottom:8px;">BUKU YANG DIKEMBALIKAN:</div>
                        <div id="booksList"></div>
                        <div id="detailIdsContainer" class="d-none"></div>
                    </div>

                    {{-- Return Form --}}
                    <form method="POST" action="{{ route('admin.returns.store', 'BORROWING_ID_PLACEHOLDER') }}" id="returnForm">
                        @csrf
                        <input type="hidden" name="detail_ids" id="detailIdsInput" value="">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.8rem; font-family:'Fredoka One',cursive; letter-spacing:1px;">KONDISI BUKU</label>
                            <select name="condition" class="form-select" style="border:3px solid var(--comic-dark); border-radius:0; font-weight:800;" required>
                                <option value="Baik">✅ Baik — Normal</option>
                                <option value="Rusak Ringan">⚠️ Rusak Ringan</option>
                                <option value="Rusak Berat">❌ Rusak Berat</option>
                                <option value="Hilang">🚫 Buku Hilang</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.8rem; font-family:'Fredoka One',cursive; letter-spacing:1px;">TANGGAL KEMBALI</label>
                            <input type="date" name="return_date" class="form-control" style="border:3px solid var(--comic-dark); border-radius:0; font-weight:800;"
                                   value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.8rem; font-family:'Fredoka One',cursive; letter-spacing:1px;">CATATAN</label>
                            <textarea name="notes" class="form-control" rows="2" style="border:3px solid var(--comic-dark); border-radius:0;" placeholder="Opsional..."></textarea>
                        </div>
                        <button type="submit" class="btn w-100 fw-bold"
                            style="background:var(--comic-orange); color:#fff; border:4px solid var(--comic-dark); box-shadow:5px 5px 0 var(--comic-dark); border-radius:0; font-family:'Bangers',cursive; font-size:1.1rem; letter-spacing:2px; padding:14px;">
                            ✅ PROSES PENGEMBALIAN
                        </button>
                    </form>
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

    async function initScanner() {
        const select = document.getElementById('camera-select');
        const reader = document.getElementById('qr-reader');

        let devices = [];
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            stream.getTracks().forEach(t => t.stop());
            devices = await Html5Qrcode.getCameras();
        } catch (e) {
            reader.innerHTML = '<div style="padding:30px; text-align:center; font-family:\'Fredoka One\',cursive; color:var(--comic-red);">Kamera tidak tersedia.<br>Gunakan input manual.</div>';
            return;
        }

        if (!devices || devices.length === 0) {
            reader.innerHTML = '<div style="padding:30px; text-align:center; font-family:\'Fredoka One\',cursive; color:var(--comic-red);">Tidak ada kamera terdeteksi.</div>';
            return;
        }

        select.innerHTML = devices.map((d, i) =>
            `<option value="${d.id}">${d.label || 'Kamera ' + (i+1)}</option>`
        ).join('');

        scanner = new Html5Qrcode('qr-reader');

        async function startCam(id) {
            if (scanner) { try { await scanner.stop(); } catch(e){} }
            try {
                await scanner.start(id, { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                    handleScan, () => {});
            } catch(e) {
                document.getElementById('scanStatus').textContent = 'Error: ' + (e.message || e);
            }
        }

        select.addEventListener('change', function () {
            if (this.value) startCam(this.value);
        });

        await startCam(devices[0].id);
        select.value = devices[0].id;
    }

    function handleScan(decodedText) {
        const statusEl = document.getElementById('scanStatus');
        statusEl.textContent = 'QR Terdeteksi: ' + decodedText;
        statusEl.style.color = 'var(--comic-orange)';

        // Parse return code: RET-{transaction_code}
        if (!decodedText.startsWith('RET-')) {
            statusEl.textContent = '⚠️ Bukan QR Return. Coba lagi.';
            statusEl.style.color = 'var(--comic-red)';
            return;
        }

        const transactionCode = decodedText.replace('RET-', '');
        fetchBorrowing(transactionCode);
    }

    async function fetchBorrowing(code) {
        try {
            const res = await fetch(`/api/borrowings/by-code?code=${encodeURIComponent(code)}`);
            const json = await res.json();

            if (!json.success) {
                document.getElementById('scanStatus').textContent = '❌ ' + (json.error || 'Tidak ditemukan');
                document.getElementById('scanStatus').style.color = 'var(--comic-red)';
                return;
            }

            // Fill form with borrowing data then show
            fillFormData(json.data);
            showForm(json.data);
        } catch (e) {
            document.getElementById('scanStatus').textContent = '❌ Gagal mengambil data.';
            document.getElementById('scanStatus').style.color = 'var(--comic-red)';
        }
    }

    function fillFormData(data) {
        document.getElementById('trxCode').textContent = data.transaction_code;
        document.getElementById('memberName').textContent = data.member.name + ' — ' + data.member.nis_nim;

        const booksList = document.getElementById('booksList');
        const unreturnedBooks = data.books.filter(b => b.status === 'borrowed');
        booksList.innerHTML = unreturnedBooks.length
            ? unreturnedBooks.map(b => `
                <div class="d-flex align-items-center gap-2 mb-2" style="padding:8px 12px; border:2px solid var(--comic-dark); background:#fff; box-shadow:2px 2px 0 var(--comic-dark);">
                    <div style="font-size:1.3rem;">📕</div>
                    <div>
                        <div style="font-family:'Fredoka One',cursive; font-size:0.85rem; color:var(--comic-dark);">${b.title}</div>
                        <div style="font-size:0.7rem; color:#888; font-weight:700;">${b.book_code} • ${b.author}</div>
                    </div>
                </div>
            `).join('')
            : '<div style="font-family:\'Fredoka One\',cursive; font-size:0.85rem; color:#888;">Semua buku sudah dikembalikan.</div>';

        // Set form action to correct borrowing ID
        const form = document.getElementById('returnForm');
        form.action = '/admin/returns/' + data.id;

        // Set detail_ids as hidden input
        document.getElementById('detailIdsInput').value = unreturnedBooks.map(b => b.id).join(',');

        // Pre-fill return date to today
        const returnDateInput = form.querySelector('[name="return_date"]');
        returnDateInput.value = new Date().toISOString().split('T')[0];
    }

    function showForm(data) {
        const formCard = document.getElementById('formCard');
        const formEmpty = document.getElementById('formEmpty');
        const formData = document.getElementById('formData');

        formCard.style.opacity = '1';
        formCard.style.pointerEvents = 'auto';
        formEmpty.classList.add('d-none');
        formData.classList.remove('d-none');

        document.getElementById('scanStatus').textContent = '✅ Data dimuat: ' + data.transaction_code;
        document.getElementById('scanStatus').style.color = 'var(--comic-green)';
    }

    initScanner();
})();
</script>
@endpush
