@extends('layouts.app')

@section('title', 'Laporan Keterlambatan per Anggota')
@section('page-title', 'Laporan Keterlambatan per Anggota')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Sistem</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Laporan</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Keterlambatan per Anggota</li>
</ul>
@endsection

@push('custom-css')
<style>
.report-card { border: 3px solid var(--comic-dark) !important; box-shadow: 4px 4px 0 var(--comic-dark) !important; border-radius: 0 !important; }
.report-card .card-header { background: var(--comic-dark) !important; border-bottom: 3px solid var(--comic-orange) !important; padding: 14px 20px; }
.report-card .card-header .card-title { font-family: 'Bangers', cursive !important; letter-spacing: 2px !important; color: var(--comic-orange) !important; font-size: 1.1rem !important; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Laporan</a>
    @if($selectedMember)
        <a href="{{ route('admin.reports.fines.by-member-pdf', $selectedMember->id) }}" target="_blank" class="btn btn-comic" style="background:var(--comic-red)!important;color:#fff!important;border-color:var(--comic-dark)!important;box-shadow:3px 3px 0 var(--comic-dark)!important;border-radius:0;">
            <i class="ki-duotone ki-file-down fs-5"></i> Download PDF
        </a>
    @endif
</div>

<div class="card report-card mb-5">
    <div class="card-header">
        <div class="card-title">🔍 PILIH ANGGOTA</div>
    </div>
    <div class="card-body p-4">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-fill">
                <label class="form-label fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Anggota</label>
                <select name="member_id" class="form-select form-select-solid" onchange="this.form.submit()">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ $selectedMember?->id == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->class ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($selectedMember)
<div class="card report-card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title">📋 KETERLAMBATAN - {{ $selectedMember->name }}</div>
        <span class="badge badge-light-danger" style="font-size:0.82rem;border-radius:0!important;border:2px solid var(--comic-red)!important;color:var(--comic-red)!important;">
            {{ $fines->count() }} keterlambatan
        </span>
    </div>
    <div class="card-body p-4">
        <div class="comic-table-wrap">
            <table class="table align-middle">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:40px;">#</th>
                        <th style="min-width:100px;">Tanggal Pinjam</th>
                        <th style="min-width:200px;">Buku</th>
                        <th style="min-width:90px;">Jatuh Tempo</th>
                        <th style="min-width:80px;">Hari</th>
                        <th style="min-width:100px;">Status</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold">
                    @forelse($fines as $index => $fine)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="white-space:nowrap;">{{ $fine->borrowing->loan_date->format('d M Y') }}</td>
                        <td>
                            @foreach($fine->borrowing->details as $detail)
                                <div style="font-size:0.82rem;">{{ Str::limit($detail->book->title ?? '-', 30) }}</div>
                            @endforeach
                        </td>
                        <td style="white-space:nowrap;">{{ $fine->borrowing->due_date->format('d M Y') }}</td>
                        <td>{{ $fine->days_late }} hari</td>
                        <td>
                            @if($fine->status === 'paid')
                                <span class="badge badge-light-success" style="font-size:0.78rem;border-radius:0!important;">Lunas</span>
                            @else
                                <span class="badge badge-light-warning" style="font-size:0.78rem;border-radius:0!important;">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-6">Tidak ada data keterlambatan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@else
<div class="card report-card">
    <div class="card-body p-5 text-center">
        <span style="font-size:3rem;">👤</span>
        <div class="mt-3 fw-bold" style="font-size:1rem;">Pilih anggota untuk melihat riwayat keterlambatan</div>
        <div class="text-muted mt-2" style="font-size:0.85rem;">Seluruh data anggota tersedia dalam daftar di atas</div>
    </div>
</div>
@endif

@endsection
