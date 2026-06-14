<div class="modal fade" id="modal-add-book" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-family:'Bangers',cursive; letter-spacing:2px; color:var(--comic-orange);">
                    📕 TAMBAH BUKU BARU
                </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form class="form" method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📖 KODE BUKU *</label>
                            <input type="text" name="book_code" class="form-control @if($errors?->has('book_code')) is-invalid @endif" placeholder="BK-0001" value="{{ old('book_code') }}" required/>
                            @if($errors?->has('book_code'))<div class="invalid-feedback d-block">{{ $errors->first('book_code') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📋 ISBN</label>
                            <input type="text" name="isbn" class="form-control @if($errors?->has('isbn')) is-invalid @endif" placeholder="978-xxx" value="{{ old('isbn') }}"/>
                            @if($errors?->has('isbn'))<div class="invalid-feedback d-block">{{ $errors->first('isbn') }}</div>@endif
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📚 JUDUL BUKU *</label>
                            <input type="text" name="title" class="form-control @if($errors?->has('title')) is-invalid @endif" placeholder="Judul lengkap buku" value="{{ old('title') }}" required/>
                            @if($errors?->has('title'))<div class="invalid-feedback d-block">{{ $errors->first('title') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📂 KATEGORI</label>
                            <select name="category_id" id="create-category-id" class="form-select @if($errors?->has('category_id')) is-invalid @endif">
                                <option value="">— Pilih Kategori —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->icon ?? '' }} {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors?->has('category_id'))<div class="invalid-feedback d-block">{{ $errors->first('category_id') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">✍️ PENULIS</label>
                            <input type="text" name="author" class="form-control @if($errors?->has('author')) is-invalid @endif" placeholder="Nama penulis" value="{{ old('author') }}"/>
                            @if($errors?->has('author'))<div class="invalid-feedback d-block">{{ $errors->first('author') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">🏢 PENERBIT</label>
                            <input type="text" name="publisher" class="form-control @if($errors?->has('publisher')) is-invalid @endif" placeholder="Nama penerbit" value="{{ old('publisher') }}"/>
                            @if($errors?->has('publisher'))<div class="invalid-feedback d-block">{{ $errors->first('publisher') }}</div>@endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📅 TAHUN</label>
                            <input type="number" name="year" class="form-control @if($errors?->has('year')) is-invalid @endif" placeholder="2024" value="{{ old('year') }}"/>
                            @if($errors?->has('year'))<div class="invalid-feedback d-block">{{ $errors->first('year') }}</div>@endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📦 STOK *</label>
                            <input type="number" name="stock" class="form-control @if($errors?->has('stock')) is-invalid @endif" value="{{ old('stock', 1) }}" min="0" required/>
                            @if($errors?->has('stock'))<div class="invalid-feedback d-block">{{ $errors->first('stock') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📍 LOKASI RAK</label>
                            <input type="text" name="rack_location" class="form-control @if($errors?->has('rack_location')) is-invalid @endif" placeholder="Contoh: Rak A-1" value="{{ old('rack_location') }}"/>
                            @if($errors?->has('rack_location'))<div class="invalid-feedback d-block">{{ $errors->first('rack_location') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">⚡ STATUS</label>
                            <select name="status" class="form-select @if($errors?->has('status')) is-invalid @endif" required>
                                <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>✅ Available</option>
                                <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>⛔ Unavailable</option>
                            </select>
                            @if($errors?->has('status'))<div class="invalid-feedback d-block">{{ $errors->first('status') }}</div>@endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📦 KONDISI</label>
                            <select name="kondisi" class="form-select @if($errors?->has('kondisi')) is-invalid @endif" required>
                                <option value="normal" {{ old('kondisi', 'normal') === 'normal' ? 'selected' : '' }}>✅ Normal</option>
                                <option value="rusak" {{ old('kondisi') === 'rusak' ? 'selected' : '' }}>⚠️ Rusak</option>
                                <option value="hilang" {{ old('kondisi') === 'hilang' ? 'selected' : '' }}>❌ Hilang</option>
                            </select>
                            @if($errors?->has('kondisi'))<div class="invalid-feedback d-block">{{ $errors->first('kondisi') }}</div>@endif
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">🖼️ SAMPUL BUKU</label>
                            <input type="file" name="cover" class="form-control @if($errors?->has('cover')) is-invalid @endif" accept="image/*"/>
                            @if($errors?->has('cover'))<div class="invalid-feedback d-block">{{ $errors->first('cover') }}</div>@endif
                            <div style="font-size:0.72rem; color:#aaa; font-weight:700; margin-top:4px;">Format: JPG, PNG. Maksimal 2MB.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-comic">
                        <i class="ki-duotone ki-check fs-4" style="color:#fff !important;"></i> SIMPAN BUKU
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>