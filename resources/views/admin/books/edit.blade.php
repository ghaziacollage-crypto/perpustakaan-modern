@extends('layouts.app')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

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
    <li class="breadcrumb-item text-gray-900">{{ Str::limit($book->title, 20) }}</li>
</ul>
@endsection

@push('custom-css')
<style>
/── Shared Card ──/
.book-card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 6px 6px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
}
.book-card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.book-card .card-title {
    font-family: 'Bangers', cursive !important;
    color: var(--comic-orange) !important;
    font-size: 1.2rem !important;
    letter-spacing: 3px !important;
    margin: 0;
}

/── Header Buttons ──/
.btn-header {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Fredoka One', cursive;
    font-size: 0.75rem;
    font-weight: 900;
    letter-spacing: 1px;
    padding: 8px 16px;
    border-radius: 0;
    border: 2px solid;
    transition: all 0.2s ease;
    text-decoration: none;
    white-space: nowrap;
}
.btn-header-back {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border-color: rgba(255,255,255,0.3);
    box-shadow: none;
}
.btn-header-back:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
}
.btn-header-cancel {
    background: #eee;
    color: var(--comic-dark);
    border-color: var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
}
.btn-header-cancel:hover {
    background: var(--comic-cream);
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark);
}

/── Form Fields ──/
.form-label {
    display: block;
    font-family: 'Fredoka One', cursive;
    font-size: 0.68rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--comic-dark);
    margin-bottom: 5px;
    font-weight: 900;
}
.form-label .required-star {
    color: var(--comic-red);
    margin-left: 2px;
}
.form-control, .form-select {
    border: 2px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    box-shadow: 3px 3px 0 var(--comic-dark) !important;
    font-family: 'Fredoka One', cursive;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--comic-dark) !important;
    padding: 10px 14px !important;
    transition: all 0.2s ease;
}
.form-control:focus, .form-select:focus {
    border-color: var(--comic-orange) !important;
    box-shadow: 4px 4px 0 var(--comic-orange) !important;
    outline: none !important;
}
.form-control::placeholder {
    color: #bbb !important;
    font-weight: 700 !important;
}
.is-invalid {
    border-color: var(--comic-red) !important;
    box-shadow: 3px 3px 0 var(--comic-red) !important;
}
.invalid-feedback {
    font-family: 'Fredoka One', cursive;
    font-size: 0.72rem;
    color: var(--comic-red);
    letter-spacing: 1px;
}

/── Cover Preview ──/
.cover-preview {
    display: flex;
    align-items: center;
    gap: 12px;
}
.cover-preview img {
    width: 70px;
    height: 95px;
    object-fit: cover;
    border: 2px solid var(--comic-dark);
    box-shadow: 2px 2px 0 var(--comic-dark);
    border-radius: 0;
}
.cover-preview-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.72rem;
    color: #aaa;
    letter-spacing: 1px;
    font-weight: 700;
}

/── Submit Buttons ──/
.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: var(--comic-orange);
    color: #fff;
    border: 3px solid var(--comic-dark);
    box-shadow: 4px 4px 0 var(--comic-dark);
    font-family: 'Fredoka One', cursive;
    font-size: 1rem;
    font-weight: 900;
    letter-spacing: 2px;
    padding: 14px 28px;
    border-radius: 0;
    transition: all 0.2s ease;
    flex: 1;
}
.btn-submit:hover {
    background: var(--comic-yellow);
    color: var(--comic-dark);
    transform: translateY(-2px);
    box-shadow: 5px 5px 0 var(--comic-dark);
}
.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eee;
    color: var(--comic-dark);
    border: 2px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    font-family: 'Fredoka One', cursive;
    font-size: 0.88rem;
    font-weight: 900;
    letter-spacing: 1px;
    padding: 12px 20px;
    border-radius: 0;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-cancel:hover {
    background: var(--comic-cream);
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark);
}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="d-flex align-items-center gap-3 p-3 mb-4"
            style="background:#fff;border:3px solid var(--comic-dark);box-shadow:4px 4px 0 var(--comic-red);border-radius:0;">
            <i class="ki-duotone ki-cross-circle fs-2" style="color:var(--comic-red);"></i>
            <div>
                <strong style="font-family:'Fredoka One',cursive;letter-spacing:1px;color:var(--comic-red);">GAGAL MENYIMPAN PERUBAHAN</strong>
                <ul class="mb-0 mt-1" style="font-size:0.82rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="card book-card">
            <div class="card-header">
                <div class="card-title">
                    ✏️ EDIT BUKU
                </div>
                <a href="{{ route('admin.books.show', $book) }}" class="btn-header btn-header-back">
                    <i class="ki-duotone ki-left fs-4"></i> Batal
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.books.update', $book) }}"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- Kode Buku --}}
                        <div class="col-md-6">
                            <label class="form-label">📖 KODE BUKU <span class="required-star">*</span></label>
                            <input type="text" name="book_code"
                                class="form-control @if($errors?->has('book_code')) is-invalid @endif"
                                placeholder="Contoh: BK-0001"
                                value="{{ old('book_code', $book->book_code) }}" required/>
                            @if($errors?->has('book_code'))
                                <div class="invalid-feedback">{{ $errors->first('book_code') }}</div>
                            @endif
                        </div>

                        {{-- ISBN --}}
                        <div class="col-md-6">
                            <label class="form-label">📋 ISBN</label>
                            <input type="text" name="isbn"
                                class="form-control @if($errors?->has('isbn')) is-invalid @endif"
                                placeholder="978-xxx-xxx-xxx-x"
                                value="{{ old('isbn', $book->isbn) }}"/>
                            @if($errors?->has('isbn'))
                                <div class="invalid-feedback">{{ $errors->first('isbn') }}</div>
                            @endif
                        </div>

                        {{-- Judul --}}
                        <div class="col-12">
                            <label class="form-label">📚 JUDUL BUKU <span class="required-star">*</span></label>
                            <input type="text" name="title"
                                class="form-control @if($errors?->has('title')) is-invalid @endif"
                                placeholder="Judul lengkap buku"
                                value="{{ old('title', $book->title) }}" required/>
                            @if($errors?->has('title'))
                                <div class="invalid-feedback">{{ $errors->first('title') }}</div>
                            @endif
                        </div>

                        {{-- Kategori --}}
                        <div class="col-md-6">
                            <label class="form-label">📂 KATEGORI</label>
                            <select name="category_id"
                                class="form-select @if($errors?->has('category_id')) is-invalid @endif">
                                <option value="">— Pilih Kategori —</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->icon ? $cat->icon . ' ' : '' }}{{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                            @if($errors?->has('category_id'))
                                <div class="invalid-feedback">{{ $errors->first('category_id') }}</div>
                            @endif
                        </div>

                        {{-- Penulis --}}
                        <div class="col-md-6">
                            <label class="form-label">✍️ PENULIS</label>
                            <input type="text" name="author"
                                class="form-control @if($errors?->has('author')) is-invalid @endif"
                                placeholder="Nama penulis"
                                value="{{ old('author', $book->author) }}"/>
                            @if($errors?->has('author'))
                                <div class="invalid-feedback">{{ $errors->first('author') }}</div>
                            @endif
                        </div>

                        {{-- Penerbit --}}
                        <div class="col-md-6">
                            <label class="form-label">🏢 PENERBIT</label>
                            <input type="text" name="publisher"
                                class="form-control @if($errors?->has('publisher')) is-invalid @endif"
                                placeholder="Nama penerbit"
                                value="{{ old('publisher', $book->publisher) }}"/>
                            @if($errors?->has('publisher'))
                                <div class="invalid-feedback">{{ $errors->first('publisher') }}</div>
                            @endif
                        </div>

                        {{-- Tahun Terbit --}}
                        <div class="col-md-3">
                            <label class="form-label">📅 TAHUN</label>
                            <input type="number" name="year"
                                class="form-control @if($errors?->has('year')) is-invalid @endif"
                                placeholder="{{ date('Y') }}"
                                value="{{ old('year', $book->year) }}"
                                min="1900" max="{{ date('Y') }}"/>
                            @if($errors?->has('year'))
                                <div class="invalid-feedback">{{ $errors->first('year') }}</div>
                            @endif
                        </div>

                        {{-- Stok --}}
                        <div class="col-md-3">
                            <label class="form-label">📦 STOK <span class="required-star">*</span></label>
                            <input type="number" name="stock"
                                class="form-control @if($errors?->has('stock')) is-invalid @endif"
                                value="{{ old('stock', $book->stock) }}"
                                min="0" required/>
                            @if($errors?->has('stock'))
                                <div class="invalid-feedback">{{ $errors->first('stock') }}</div>
                            @endif
                        </div>

                        {{-- Lokasi Rak --}}
                        <div class="col-md-6">
                            <label class="form-label">📍 LOKASI RAK</label>
                            <input type="text" name="rack_location"
                                class="form-control @if($errors?->has('rack_location')) is-invalid @endif"
                                placeholder="Contoh: Rak A-1 / Lokasi-3"
                                value="{{ old('rack_location', $book->rack_location) }}"/>
                            @if($errors?->has('rack_location'))
                                <div class="invalid-feedback">{{ $errors->first('rack_location') }}</div>
                            @endif
                        </div>

                        {{-- Sinopsis --}}
                        <div class="col-12">
                            <label class="form-label">📝 SINOPSIS</label>
                            <textarea name="synopsis"
                                class="form-control @if($errors?->has('synopsis')) is-invalid @endif"
                                rows="5"
                                placeholder="Tulis ringkasan atau deskripsi singkat buku...">{{ old('synopsis', $book->synopsis) }}</textarea>
                            @if($errors?->has('synopsis'))
                                <div class="invalid-feedback">{{ $errors->first('synopsis') }}</div>
                            @endif
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">⚡ STATUS <span class="required-star">*</span></label>
                            <select name="status"
                                class="form-select @if($errors?->has('status')) is-invalid @endif" required>
                                <option value="available"
                                    {{ old('status', $book->status->value) === 'available' ? 'selected' : '' }}>
                                    ✅ Available — Buku dapat dipinjam
                                </option>
                                <option value="unavailable"
                                    {{ old('status', $book->status->value) === 'unavailable' ? 'selected' : '' }}>
                                    ⛔ Unavailable — Buku tidak dapat dipinjam
                                </option>
                            </select>
                            @if($errors?->has('status'))
                                <div class="invalid-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                        {{-- Kondisi --}}
                        <div class="col-md-6">
                            <label class="form-label">📦 KONDISI <span class="required-star">*</span></label>
                            <select name="kondisi"
                                class="form-select @if($errors?->has('kondisi')) is-invalid @endif" required>
                                <option value="normal"
                                    {{ old('kondisi', $book->kondisi->value ?? 'normal') === 'normal' ? 'selected' : '' }}>
                                    ✅ Normal
                                </option>
                                <option value="rusak"
                                    {{ old('kondisi', $book->kondisi->value ?? 'normal') === 'rusak' ? 'selected' : '' }}>
                                    ⚠️ Rusak
                                </option>
                                <option value="hilang"
                                    {{ old('kondisi', $book->kondisi->value ?? 'normal') === 'hilang' ? 'selected' : '' }}>
                                    ❌ Hilang
                                </option>
                            </select>
                            @if($errors?->has('kondisi'))
                                <div class="invalid-feedback">{{ $errors->first('kondisi') }}</div>
                            @endif
                        </div>

                        {{-- Sampul Buku --}}
                        <div class="col-12">
                            <label class="form-label">🖼️ SAMPUL BUKU</label>
                            <input type="file" name="cover"
                                class="form-control @if($errors?->has('cover')) is-invalid @endif"
                                accept="image/jpg,image/jpeg,image/png"/>
                            @if($errors?->has('cover'))
                                <div class="invalid-feedback">{{ $errors->first('cover') }}</div>
                            @endif
                            <div style="font-size:0.68rem; color:#aaa; font-weight:700; margin-top:4px; letter-spacing:1px;">
                                Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengganti.
                            </div>
                            @if($book->cover)
                            <div class="cover-preview mt-3">
                                <img src="{{ asset('storage/' . $book->cover) }}" alt="Cover saat ini"/>
                                <span class="cover-preview-label">Sampul saat ini</span>
                            </div>
                            @endif
                        </div>

                        {{-- Hapus Cover --}}
                        @if($book->cover)
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="_remove_cover" id="remove_cover" class="form-check-input" value="1"/>
                                <label for="remove_cover" class="form-check-label"
                                    style="font-family:'Fredoka One', cursive; font-size:0.75rem; color:var(--comic-red); letter-spacing:1px;">
                                    🗑️ Hapus sampul buku
                                </label>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('admin.books.show', $book) }}" class="btn-cancel">
                            <i class="ki-duotone ki-left fs-4"></i> Batal
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="ki-duotone ki-check fs-4"></i> SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
