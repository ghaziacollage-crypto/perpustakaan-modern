@extends('layouts.app')

@section('title', 'WhatsApp Notifikasi')
@section('page-title', 'WhatsApp Notifikasi')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Pengaturan</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">WhatsApp</li>
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
.wa-response {
    background: #111827;
    color: #d1fae5;
    border: 2px solid var(--comic-dark);
    padding: 14px;
    max-height: 320px;
    overflow: auto;
    white-space: pre-wrap;
    font-size: 0.78rem;
}
.wa-status {
    border: 2px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    padding: 10px 12px;
    background: #fff;
    font-weight: 800;
}
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap gap-3 mb-5">
    <a href="{{ route('admin.whatsapp.bulk') }}" class="btn btn-comic">Pengiriman Massal</a>
</div>

<div class="row g-5">
    <div class="col-xl-5">
        <div class="card wa-panel h-100">
            <div class="card-header">
                <div class="card-title">Status Koneksi API</div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="wa-status">Sumber konfigurasi: {{ $connection['config_source'] }}</div>
                    </div>
                    <div class="col-12">
                        <div class="wa-status">API aktif: {{ $connection['is_active'] ? 'Ya' : 'Tidak' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="wa-status">Base URL: {{ $connection['base_url'] ?: '-' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="wa-status">Session ID: {{ $connection['session_id'] ?: '-' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="wa-status">Sender: {{ $connection['sender'] ?: '-' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="wa-status">Token: {{ $connection['has_token'] ? 'Tersedia' : 'Kosong' }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.whatsapp.health') }}">
                    @csrf
                    <button type="submit" class="btn btn-comic w-100">Cek Health API</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card wa-panel mb-5">
            <div class="card-header">
                <div class="card-title">Tes Kirim Pesan</div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.whatsapp.test-message') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nomor WhatsApp Tujuan</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="08xxxxxxxxxx" required>
                        @error('phone')<div class="text-danger fw-bold mt-2">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Isi Pesan</label>
                        <textarea name="message" rows="5" class="form-control" required>{{ old('message', 'Test koneksi WhatsApp dari aplikasi perpustakaan-modern.') }}</textarea>
                        @error('message')<div class="text-danger fw-bold mt-2">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-comic">Kirim Pesan Test</button>
                </form>
            </div>
        </div>

        @if($lastHealth)
        <div class="card wa-panel mb-5">
            <div class="card-header">
                <div class="card-title">Response Health API</div>
            </div>
            <div class="card-body p-4">
                <div class="alert {{ $lastHealth['success'] ? 'alert-success' : 'alert-danger' }}" style="border-radius:0; font-weight:800;">
                    {{ $lastHealth['message'] }} @if($lastHealth['status_code']) (HTTP {{ $lastHealth['status_code'] }}) @endif
                </div>
                <pre class="wa-response">{{ json_encode($lastHealth['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        @if($lastTest)
        <div class="card wa-panel">
            <div class="card-header">
                <div class="card-title">Response Tes Kirim Pesan</div>
            </div>
            <div class="card-body p-4">
                <div class="alert {{ $lastTest['success'] ? 'alert-success' : 'alert-danger' }}" style="border-radius:0; font-weight:800;">
                    {{ $lastTest['message'] }} @if($lastTest['status_code']) (HTTP {{ $lastTest['status_code'] }}) @endif
                </div>
                <pre class="wa-response">{{ json_encode($lastTest['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
