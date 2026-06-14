<div class="modal fade" id="modal-edit-book" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-family:'Bangers',cursive; letter-spacing:2px; color:var(--comic-orange);">
                    ✏️ EDIT BUKU
                </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form class="form" method="POST" action="#" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📖 KODE BUKU *</label>
                            <input type="text" name="book_code" class="form-control" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📋 ISBN</label>
                            <input type="text" name="isbn" class="form-control"/>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📚 JUDUL BUKU *</label>
                            <input type="text" name="title" class="form-control" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📂 KATEGORI</label>
                            <select name="category_id" id="edit-category-id" class="form-select">
                                <option value="">— Pilih Kategori —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->icon ?? '' }} {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">✍️ PENULIS</label>
                            <input type="text" name="author" class="form-control"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">🏢 PENERBIT</label>
                            <input type="text" name="publisher" class="form-control"/>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📅 TAHUN</label>
                            <input type="number" name="year" class="form-control"/>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📦 STOK *</label>
                            <input type="number" name="stock" class="form-control" min="0" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📍 LOKASI RAK</label>
                            <input type="text" name="rack_location" class="form-control"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">⚡ STATUS</label>
                            <select name="status" class="form-select" required>
                                <option value="available">✅ Available</option>
                                <option value="unavailable">⛔ Unavailable</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">📦 KONDISI</label>
                            <select name="kondisi" class="form-select" required>
                                <option value="normal">✅ Normal</option>
                                <option value="rusak">⚠️ Rusak</option>
                                <option value="hilang">❌ Hilang</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-family:'Fredoka One',cursive; font-size:0.75rem; letter-spacing:1px; text-transform:uppercase; font-weight:900;">🖼️ SAMPUL BUKU</label>
                            <input type="file" name="cover" class="form-control" accept="image/*"/>
                            <div style="font-size:0.72rem; color:#aaa; font-weight:700; margin-top:4px;">Format: JPG, PNG. Maksimal 2MB.</div>

                            {{-- Cover preview (shown by JS when editing a book with cover) --}}
                            <div id="edit-cover-preview" class="mt-3" style="display:none;">
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <img id="edit-cover-img" src="" alt="Cover" style="width:60px;height:80px;object-fit:cover;border:2px solid var(--comic-dark);box-shadow:3px 3px 0 var(--comic-dark);"/>
                                    <div>
                                        <label class="form-check" style="cursor:pointer;">
                                            <input type="checkbox" name="_remove_cover" value="1" class="form-check-input" style="accent-color:var(--comic-orange);"/>
                                            <span style="font-family:'Fredoka One',cursive;font-size:0.7rem;color:var(--comic-red);letter-spacing:1px;">🗑️ HAPUS SAMPUL</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-comic">
                        <i class="ki-duotone ki-check fs-4" style="color:#fff !important;"></i> SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>