@extends('layouts.app')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.books.index') }}" class="text-muted text-hover-primary">Data Buku</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Tambah</li>
</ul>
@endsection

@push('custom-css')
<style>
.form-card {
    border: 3px solid var(--comic-dark);
    box-shadow: 6px 6px 0 var(--comic-dark);
    border-radius: 0;
}
.form-card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
    padding: 16px 24px;
}
.form-card .card-header .card-title {
    font-family: 'Bangers', cursive !important;
    color: var(--comic-orange) !important;
    letter-spacing: 2px;
    font-size: 1.1rem;
}
.form-group {
    margin-bottom: 16px;
}
.form-label-comic {
    display: block;
    font-family: 'Fredoka One', cursive;
    font-size: 0.72rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--comic-dark);
    margin-bottom: 6px;
    font-weight: 900;
}
.form-label-comic .required-star {
    color: var(--comic-red);
    margin-left: 2px;
}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center mb-4"
            style="border:3px solid var(--comic-dark);box-shadow:4px 4px 0 var(--comic-red);border-radius:0;background:#fff;">
            <i class="ki-duotone ki-cross-circle fs-2 me-3" style="color:var(--comic-red);"></i>
            <div>
                <strong style="font-family:'Fredoka One',cursive;letter-spacing:1px;">GAGAL MENYIMPAN BUKU</strong>
                <ul class="mb-0 mt-1" style="font-size:0.82rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="card form-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title">
                    📕 TAMBAH BUKU BARU
                </div>
                <a href="{{ route('admin.books.index') }}" class="btn btn-sm"
                    style="
                    background:rgba(255,255,255,0.1);color:#fff;
                    border:2px solid rgba(255,255,255,0.3);
                    border-radius:0;font-family:'Fredoka One',cursive;
                    font-size:0.75rem;letter-spacing:1px;">
                    ← Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.books.store') }}"
                    enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="row g-4">
                        {{-- Kode Buku --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">
                                📖 KODE BUKU <span class="required-star">*</span>
                            </label>
                            <input type="text" name="book_code"
                                class="form-control @if($errors?->has('book_code')) is-invalid @endif"
                                placeholder="Contoh: BK-0001"
                                value="{{ old('book_code') }}"
                                required/>
                            @if($errors?->has('book_code'))
                                <div class="invalid-feedback d-block">{{ $errors->first('book_code') }}</div>
                            @endif
                        </div>

                        {{-- ISBN --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">📋 ISBN</label>
                            <input type="text" name="isbn"
                                class="form-control @if($errors?->has('isbn')) is-invalid @endif"
                                placeholder="978-xxx-xxx-xxx-x"
                                value="{{ old('isbn') }}"/>
                            @if($errors?->has('isbn'))
                                <div class="invalid-feedback d-block">{{ $errors->first('isbn') }}</div>
                            @endif
                        </div>

                        {{-- Judul --}}
                        <div class="col-12">
                            <label class="form-label-comic">
                                📚 JUDUL BUKU <span class="required-star">*</span>
                            </label>
                            <input type="text" name="title"
                                class="form-control @if($errors?->has('title')) is-invalid @endif"
                                placeholder="Judul lengkap buku"
                                value="{{ old('title') }}"
                                required/>
                            @if($errors?->has('title'))
                                <div class="invalid-feedback d-block">{{ $errors->first('title') }}</div>
                            @endif
                        </div>

                        {{-- Kategori --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">📂 KATEGORI</label>
                            <select name="category_id"
                                class="form-select @if($errors?->has('category_id')) is-invalid @endif">
                                <option value="">— Pilih Kategori —</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->icon ? $cat->icon . ' ' : '' }}{{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                            @if($errors?->has('category_id'))
                                <div class="invalid-feedback d-block">{{ $errors->first('category_id') }}</div>
                            @endif
                        </div>

                        {{-- Penulis --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">✍️ PENULIS</label>
                            <input type="text" name="author"
                                class="form-control @if($errors?->has('author')) is-invalid @endif"
                                placeholder="Nama penulis"
                                value="{{ old('author') }}"/>
                            @if($errors?->has('author'))
                                <div class="invalid-feedback d-block">{{ $errors->first('author') }}</div>
                            @endif
                        </div>

                        {{-- Penerbit --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">🏢 PENERBIT</label>
                            <input type="text" name="publisher"
                                class="form-control @if($errors?->has('publisher')) is-invalid @endif"
                                placeholder="Nama penerbit"
                                value="{{ old('publisher') }}"/>
                            @if($errors?->has('publisher'))
                                <div class="invalid-feedback d-block">{{ $errors->first('publisher') }}</div>
                            @endif
                        </div>

                        {{-- Tahun Terbit --}}
                        <div class="col-md-3">
                            <label class="form-label-comic">📅 TAHUN</label>
                            <input type="number" name="year"
                                class="form-control @if($errors?->has('year')) is-invalid @endif"
                                placeholder="{{ date('Y') }}"
                                value="{{ old('year') }}"
                                min="1900" max="{{ date('Y') }}"/>
                            @if($errors?->has('year'))
                                <div class="invalid-feedback d-block">{{ $errors->first('year') }}</div>
                            @endif
                        </div>

                        {{-- Stok --}}
                        <div class="col-md-3">
                            <label class="form-label-comic">
                                📦 STOK <span class="required-star">*</span>
                            </label>
                            <input type="number" name="stock"
                                class="form-control @if($errors?->has('stock')) is-invalid @endif"
                                value="{{ old('stock', 1) }}"
                                min="0" required/>
                            @if($errors?->has('stock'))
                                <div class="invalid-feedback d-block">{{ $errors->first('stock') }}</div>
                            @endif
                        </div>

                        {{-- Lokasi Rak --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">📍 LOKASI RAK</label>
                            <input type="text" name="rack_location"
                                class="form-control @if($errors?->has('rack_location')) is-invalid @endif"
                                placeholder="Contoh: Rak A-1 / Lokasi-3"
                                value="{{ old('rack_location') }}"/>
                            @if($errors?->has('rack_location'))
                                <div class="invalid-feedback d-block">{{ $errors->first('rack_location') }}</div>
                            @endif
                        </div>

                        {{-- Sinopsis --}}
                        <div class="col-12">
                            <label class="form-label-comic">📝 SINOPSIS</label>
                            <textarea name="synopsis"
                                class="form-control @if($errors?->has('synopsis')) is-invalid @endif"
                                rows="5"
                                placeholder="Tulis ringkasan atau deskripsi singkat buku...">{{ old('synopsis') }}</textarea>
                            @if($errors?->has('synopsis'))
                                <div class="invalid-feedback d-block">{{ $errors->first('synopsis') }}</div>
                            @endif
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">
                                ⚡ STATUS <span class="required-star">*</span>
                            </label>
                            <select name="status"
                                class="form-select @if($errors?->has('status')) is-invalid @endif" required>
                                <option value="available"
                                    {{ old('status', 'available') === 'available' ? 'selected' : '' }}>
                                    ✅ Available — Buku dapat dipinjam
                                </option>
                                <option value="unavailable"
                                    {{ old('status') === 'unavailable' ? 'selected' : '' }}>
                                    ⛔ Unavailable — Buku tidak dapat dipinjam
                                </option>
                            </select>
                            @if($errors?->has('status'))
                                <div class="invalid-feedback d-block">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                        {{-- Kondisi --}}
                        <div class="col-md-6">
                            <label class="form-label-comic">
                                📦 KONDISI <span class="required-star">*</span>
                            </label>
                            <select name="kondisi"
                                class="form-select @if($errors?->has('kondisi')) is-invalid @endif" required>
                                <option value="normal"
                                    {{ old('kondisi', 'normal') === 'normal' ? 'selected' : '' }}>
                                    ✅ Normal
                                </option>
                                <option value="rusak"
                                    {{ old('kondisi') === 'rusak' ? 'selected' : '' }}>
                                    ⚠️ Rusak
                                </option>
                                <option value="hilang"
                                    {{ old('kondisi') === 'hilang' ? 'selected' : '' }}>
                                    ❌ Hilang
                                </option>
                            </select>
                            @if($errors?->has('kondisi'))
                                <div class="invalid-feedback d-block">{{ $errors->first('kondisi') }}</div>
                            @endif
                        </div>

                        {{-- Sampul Buku --}}
                        <div class="col-12">
                            <label class="form-label-comic">🖼️ SAMPUL BUKU</label>
                            <input type="file" name="cover"
                                class="form-control @if($errors?->has('cover')) is-invalid @endif"
                                accept="image/jpg,image/jpeg,image/png"/>
                            @if($errors?->has('cover'))
                                <div class="invalid-feedback d-block">{{ $errors->first('cover') }}</div>
                            @endif
                            <div style="font-size:0.72rem; color:#aaa; font-weight:700; margin-top:4px;">
                                Format: JPG, PNG. Maksimal 2MB. Tidak wajib diisi.
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('admin.books.index') }}" class="btn"
                            style="
                            background:#eee; color:var(--comic-dark);
                            border:2px solid var(--comic-dark); box-shadow:3px 3px 0 var(--comic-dark);
                            font-family:'Fredoka One',cursive; font-size:0.9rem;
                            border-radius:0; font-weight:900; letter-spacing:1px; padding:12px 24px;">
                            ← Batal
                        </a>
                        <button type="submit" class="btn flex-grow-1"
                            style="
                            background:var(--comic-orange); color:#fff;
                            border:3px solid var(--comic-dark); box-shadow:4px 4px 0 var(--comic-dark);
                            font-family:'Fredoka One',cursive; font-size:1rem;
                            border-radius:0; font-weight:900; letter-spacing:2px; padding:14px 24px;
                            transition: all 0.2s ease;">
                            <i class="ki-duotone ki-check fs-4 me-2" style="color:#fff;"></i>
                            SIMPAN BUKU
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
