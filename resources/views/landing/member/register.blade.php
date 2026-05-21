@extends('landing.layout')

@section('title', 'Daftar Anggota — ' . app_setting('app_name', 'Perpustakaan'))
@section('page-title', 'Daftar Anggota')

@section('content')
{{-- Sticky Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark comic-navbar-slider py-2 sticky-top">
    <div class="container position-relative">
        <a class="navbar-brand comic-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-icon">📚</span>
            <span class="brand-text fw-black">{{ app_setting('app_name', 'Perpustakaan') }}</span>
        </a>
        <button class="navbar-toggler border-3 border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navRegister">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navRegister">
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
                    <a class="nav-link btn btn-outline-light btn-sm px-3 fw-bold" href="{{ route('member.index') }}">👤 Member</a>
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
            <div class="section-label" style="color: var(--comic-yellow);">PENDAFTARAN ANGGOTA</div>
            <h1 class="comic-section-title text-white mb-2">📝 DAFTAR <span class="text-orange">ANGGOTA</span></h1>
            <p class="text-white-50 fw-bold">Isi formulir di bawah untuk mendaftar sebagai anggota perpustakaan</p>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            @if(session('success'))
            <div class="alert alert-success" style="border: 4px solid var(--comic-dark); box-shadow: 5px 5px 0 var(--comic-dark); border-radius: 0; background: #d4edda; font-family: 'Fredoka One', cursive; letter-spacing: 1px;">
                <div style="font-size: 2rem; text-align: center; margin-bottom: 10px;">✅</div>
                <div style="text-align: center; color: var(--comic-dark); font-size: 1.1rem; margin-bottom: 10px;">
                    PENDAFTARAN BERHASIL!
                </div>
                <div style="text-align: center; color: #155724; font-size: 0.85rem; font-weight: 700; line-height: 1.6;">
                    {{ session('success') }}<br><br>
                    Anda akan menerima QR code setelah akun disetujui oleh admin.
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('member.index') }}" class="btn btn-dark fw-bold" style="border-radius: 0; border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark); padding: 10px 24px;">
                        🔙 Kembali ke Member Area
                    </a>
                </div>
            </div>
            @endif

            <div class="card" style="border: 4px solid var(--comic-dark); box-shadow: 6px 6px 0 var(--comic-dark); border-radius: 0;">
                <div class="card-header" style="background: var(--comic-dark); border-bottom: 4px solid var(--comic-orange);">
                    <div class="card-title mb-0" style="font-family: 'Bangers', cursive; font-size: 1.5rem; letter-spacing: 2px; color: var(--comic-orange);">
                        📝 FORMULIR PENDAFTARAN
                    </div>
                </div>
                <div class="card-body" style="background: var(--comic-cream); padding: 30px;">
                    <form method="POST" action="{{ route('member.register.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- NISN --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                NISN <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nis_nim" class="form-control @error('nis_nim') is-invalid @enderror"
                                   value="{{ old('nis_nim') }}"
                                   placeholder="Masukkan NISN Anda"
                                   style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 800;"
                                   required>
                            @error('nis_nim')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                            <small class="form-text" style="color: #888; font-weight: 700; font-size: 0.75rem;">
                                💡 NISN adalah Nomor Induk Siswa Nasional (10 digit)
                            </small>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                NAMA LENGKAP <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Nama lengkap sesuai KTP/Kartu Pelajar"
                                   style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 800;"
                                   required>
                            @error('name')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kelas --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                KELAS
                            </label>
                            <input type="text" name="class" class="form-control @error('class') is-invalid @enderror"
                                   value="{{ old('class') }}"
                                   placeholder="Contoh: XII IPA 1"
                                   style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 800;">
                            @error('class')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jurusan --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                JURUSAN
                            </label>
                            <input type="text" name="major" class="form-control @error('major') is-invalid @enderror"
                                   value="{{ old('major') }}"
                                   placeholder="Contoh: IPA, IPS, Teknik Informatika"
                                   style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 800;">
                            @error('major')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                ALAMAT
                            </label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Alamat lengkap tempat tinggal"
                                      style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 700;">{{ old('address') }}</textarea>
                            @error('address')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- WhatsApp --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                WHATSAPP
                            </label>
                            <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror"
                                   value="{{ old('whatsapp') }}"
                                   placeholder="Contoh: 08123456789"
                                   style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 800;">
                            @error('whatsapp')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                EMAIL
                            </label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="email@example.com"
                                   style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 800;">
                            @error('email')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Foto --}}
                        <div class="mb-4">
                   <label class="form-label fw-bold" style="font-size: 0.85rem; font-family: 'Fredoka One', cursive; color: var(--comic-dark); letter-spacing: 1px;">
                                FOTO (OPSIONAL)
                            </label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror"
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   style="border: 3px solid var(--comic-dark); border-radius: 0; font-weight: 700;">
                            @error('photo')
                            <div class="invalid-feedback" style="font-weight: 700;">{{ $message }}</div>
                            @enderror
                            <small class="form-text" style="color: #888; font-weight: 700; font-size: 0.75rem;">
                                📷 Format: JPG, PNG, WEBP. Maksimal 2MB
                            </small>
                        </div>

                        {{-- Info Box --}}
                        <div class="alert alert-info" style="border: 3px solid var(--comic-blue); background: rgba(52, 152, 219, 0.1); border-radius: 0; font-weight: 700; font-size: 0.85rem; color: var(--comic-dark);">
                            <div style="font-family: 'Fredoka One', cursive; margin-bottom: 8px; letter-spacing: 1px;">
                                ℹ️ INFORMASI PENTING
                            </div>
                            <ul style="margin-bottom: 0; padding-left: 20px; line-height: 1.8;">
                                <li>Pendaftaran akan diverifikasi oleh admin perpustakaan</li>
                                <li>Anda akan menerima QR code setelah akun disetujui</li>
                                <li>Gunakan QR code untuk akses member area</li>
                            </ul>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg fw-bold"
                                    style="background: var(--comic-orange); color: #fff; border: 4px solid var(--comic-dark); box-shadow: 6px 6px 0 var(--comic-dark); border-radius: 0; font-family: 'Bangers', cursive; font-size: 1.3rem; letter-spacing: 2px; padding: 16px;">
                                📝 DAFTAR SEKARANG
                            </button>
                            <a href="{{ route('member.index') }}" class="btn btn-outline-dark btn-lg fw-bold"
                               style="border: 3px solid var(--comic-dark); box-shadow: 4px 4px 0 var(--comic-dark); border-radius: 0; font-family: 'Fredoka One', cursive; letter-spacing: 1px;">
                                🔙 Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Already Registered --}}
            <div class="text-center mt-4">
                <p style="font-family: 'Fredoka One', cursive; color: #aaa; font-size: 0.9rem; letter-spacing: 1px;">
                    Sudah punya akun?
                    <a href="{{ route('member.index') }}"
                       style="color: var(--comic-orange); text-decoration: underline; font-weight: 900;">
                        📷 Scan QR di sini
                    </a>
                </p>
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
