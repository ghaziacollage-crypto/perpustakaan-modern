@extends('layouts.app')

@section('title', 'Pengiriman WhatsApp Massal')
@section('page-title', 'Pengiriman WhatsApp Massal')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.whatsapp.index') }}" class="text-muted text-hover-primary">WhatsApp</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Massal</li>
</ul>
@endsection

@push('custom-css')
<style>
.wa-panel {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 5px 5px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
}
.wa-panel .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
}
.wa-panel .card-title {
    color: var(--comic-orange) !important;
    font-family: 'Bangers', cursive !important;
    letter-spacing: 2px !important;
}
.wa-template-help {
    border: 2px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    padding: 12px;
    background: #fff;
    font-size: 0.82rem;
    font-weight: 800;
}
.wa-invalid {
    color: var(--comic-red);
    font-weight: 900;
}
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap gap-3 mb-5">
    <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-dark" style="border-radius:0;">Test WhatsApp API</a>
</div>

@if($summary)
<div class="card wa-panel mb-5">
    <div class="card-header">
        <div class="card-title">Ringkasan Pengiriman</div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-3"><strong>Total:</strong> {{ $summary['total'] }}</div>
            <div class="col-md-3"><strong>Berhasil:</strong> {{ $summary['sent'] }}</div>
            <div class="col-md-3"><strong>Gagal:</strong> {{ $summary['failed'] }}</div>
            <div class="col-md-3"><strong>Dilewati:</strong> {{ $summary['skipped'] }}</div>
        </div>
        <div class="comic-table-wrap">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Nomor</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary['details'] as $detail)
                    <tr>
                        <td>{{ $detail['name'] }}</td>
                        <td>{{ $detail['phone'] }}</td>
                        <td>{{ $detail['status'] }}</td>
                        <td>{{ $detail['message'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<form method="POST" action="{{ route('admin.whatsapp.bulk.send') }}" id="bulkWaForm">
    @csrf
    <input type="hidden" name="mode" id="waMode" value="selected">

    <div class="card wa-panel mb-5">
        <div class="card-header">
            <div class="card-title">Template Pesan Dinamis</div>
        </div>
        <div class="card-body p-4">
            <div class="wa-template-help mb-4">
                Variabel: {nama}, {judul_buku}, {tanggal_pinjam}, {tanggal_jatuh_tempo}, {jumlah_hari_terlambat}
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <label class="form-label fw-bold">Template Jatuh Tempo Hari Ini</label>
                    <textarea name="due_today_template" class="form-control" rows="8" required>{{ old('due_today_template', $dueTodayTemplate) }}</textarea>
                    @error('due_today_template')<div class="text-danger fw-bold mt-2">{{ $message }}</div>@enderror
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">Template Terlambat</label>
                    <textarea name="overdue_template" class="form-control" rows="8" required>{{ old('overdue_template', $overdueTemplate) }}</textarea>
                    @error('overdue_template')<div class="text-danger fw-bold mt-2">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-3 mb-5">
        <button type="submit" class="btn btn-comic" data-mode="selected">Kirim ke yang Dipilih</button>
        <button type="submit" class="btn btn-comic" data-mode="due_today">Kirim ke Semua Jatuh Tempo Hari Ini</button>
        <button type="submit" class="btn btn-comic" data-mode="overdue">Kirim ke Semua yang Terlambat</button>
    </div>

    @include('admin.whatsapp.partials.recipients-table', [
        'title' => 'Peminjam Jatuh Tempo Hari Ini',
        'items' => $dueToday,
        'statusLabel' => 'Jatuh Tempo Hari Ini',
    ])

    @include('admin.whatsapp.partials.recipients-table', [
        'title' => 'Peminjam yang Terlambat',
        'items' => $overdue,
        'statusLabel' => 'Terlambat',
    ])
</form>
@endsection

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('bulkWaForm');
    var modeInput = document.getElementById('waMode');

    form.querySelectorAll('button[data-mode]').forEach(function (button) {
        button.addEventListener('click', function () {
            modeInput.value = button.getAttribute('data-mode');
        });
    });

    form.addEventListener('submit', function (event) {
        var mode = modeInput.value;
        if (mode === 'selected' && form.querySelectorAll('input[name="selected[]"]:checked').length === 0) {
            event.preventDefault();
            alert('Pilih minimal satu penerima terlebih dahulu.');
            return;
        }

        if (!confirm('Kirim pesan WhatsApp sesuai pilihan ini?')) {
            event.preventDefault();
        }
    });
});
</script>
@endpush
