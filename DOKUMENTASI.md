# Dokumentasi Aplikasi Perpustakaan Modern

Dokumen ini menjelaskan cara kerja, fitur, dan teknologi yang digunakan dalam aplikasi **Perpustakaan Modern** — sebuah sistem manajemen perpustakaan berbasis web yang dibangun dengan Laravel 12.

---

## 1. Cara Kerja Aplikasi

### 1.1 Arsitektur Umum

Aplikasi ini menggunakan arsitektur **MVC (Model-View-Controller)** dari Laravel Framework. Alur kerja utama:

```
User Request
    ↓
Route (web.php / api.php)
    ↓
Middleware (auth, throttle, role check)
    ↓
Controller (mengurus HTTP handling saja)
    ↓
Service (business logic)
    ↓
Model (Eloquent ORM)
    ↓
Database (MySQL/MariaDB)
    ↓
Response (Blade View / JSON API)
```

### 1.2 Alur Utama

#### a. Landing Page (Publik)
- Pengunjung dapat melihat katalog buku, detail buku, dan kategori tanpa login.
- Hero slides menarik perhatian di halaman depan.
- Data buku ditampilkan dengan pagination.

#### b. Sistem Autentikasi
- Login menggunakan email dan password via session-based auth.
- Setelah login, sistem memeriksa role:
  - **Super Admin / Admin** → diarahkan ke `/admin/dashboard`
  - **Guest** → diarahkan ke halaman utama

#### c. Manajemen Buku (Admin)
- Admin dapat menambah, mengedit, dan menghapus buku.
- Setiap buku mendapatkan **QR Code unik** untuk identifikasi.
- Admin bisa generate QR secara individual atau bulk.
- Cover buku di-upload dan disimpan di storage.

#### d. Sistem Peminjaman
1. Admin membuka halaman peminjaman baru.
2. Melakukan lookup anggota via QR code atau nama.
3. Melakukan lookup buku via QR code atau judul.
4. Menentukan tanggal batas pengembalian.
5. Sistem membuat record borrowing + borrowing_detail.
6. Stok buku berkurang secara otomatis.

#### e. Sistem Pengembalian
1. Admin memindai QR kode anggota atau buku.
2. Pilih transaksi peminjaman yang aktif.
3. Sistem menandai buku sebagai dikembalikan.
4. Jika terlambat, sistem otomatis membuat denda.

#### f. Denda (Fine)
- Denda dihitung berdasarkan jumlah hari keterlambatan × tarif per hari.
- Admin dapat menandai denda sebagai lunas atau belum lunas.
- Pemberitahuan denda bisa dikirim via WhatsApp.

#### g. WhatsApp Reminder
- Sistem mengirim pesan WhatsApp otomatis sebagai pengingat pengembalian.
- Semua pengiriman logged di tabel `whatsapp_logs`.

#### h. Laporan & Export
- Dashboard menampilkan statistik terkini.
- Admin dapat melihat laporan lengkap.
- Data bisa diexport ke format CSV.

---

## 2. Fitur-Fitur Aplikasi

### 2.1 Fitur Publik (Landing Page)

| Fitur | Deskripsi |
|-------|-----------|
| **Halaman Beranda** | Hero slider + statistik perpustakaan |
| **Katalog Buku** | Daftar semua buku dengan pagination |
| **Detail Buku** | Informasi lengkap buku (judul, penulis, deskripsi, cover, stok) |
| **Kategori** | Daftar kategori buku |

### 2.2 Fitur Autentikasi

| Fitur | Deskripsi |
|-------|-----------|
| **Login** | Login dengan email dan password |
| **Logout** | Mengakhiri sesi |
| **Role-based Redirect** | Arahkan sesuai role setelah login |

### 2.3 Fitur Manajemen Admin

| Fitur | Deskripsi |
|-------|-----------|
| **Dashboard** | Statistik umum: total buku, anggota, peminjaman aktif, keterlambatan |
| **Manajemen Buku** | CRUD buku lengkap dengan cover upload |
| **Manajemen Kategori** | CRUD kategori buku |
| **Manajemen Anggota** | CRUD anggota perpustakaan + QR code anggota |
| **Manajemen User** | CRUD user admin/staf + role assignment |
| **QR Code Generator** | Generate QR code individual & bulk untuk buku |
| **QR Code Printing** | Cetak QR code untuk ditempelkan di buku |
| **QR Scanner** | Antarmuka pemindaian QR code |

### 2.4 Fitur Peminjaman

| Fitur | Deskripsi |
|-------|-----------|
| **Peminjaman Baru** | Lookup anggota & buku, tentukan tanggal jatuh tempo |
| **Riwayat Peminjaman** | Lihat semua transaksi peminjaman |
| **Pengembalian** | Proses pengembalian buku via QR atau manual |
| **Denda Otomatis** | Hitung denda jika terlambat |
| **Kwitansi (Receipt)** | Generate kwitansi HTML dan PDF |
| **WhatsApp Reminder** | Kirim notifikasi pengingat via WhatsApp |

### 2.5 Fitur Laporan

| Fitur | Deskripsi |
|-------|-----------|
| **Laporan Statistik** | Grafik dan statistik peminjaman |
| **Export CSV** | Export data buku, anggota, dan peminjaman ke CSV |
| **Audit Log** | Riwayat perubahan data lengkap |
| **Laporan Denda** | Daftar denda berdasarkan status |

### 2.6 Fitur Pengaturan

| Fitur | Deskripsi |
|-------|-----------|
| **Pengaturan Umum** | Durasi pinjam, maksimal pinjam, tarif denda |
| **Pengaturan WhatsApp** | Konfigurasi API WhatsApp |

---

## 3. Teknologi yang Digunakan

### 3.1 Tech Stack Inti

| Teknologi | Versi | Kegunaan |
|-----------|-------|----------|
| **Laravel** | ^13.7 | PHP Framework utama |
| **PHP** | 8.3 | Bahasa pemrograman (strict typing, readonly, enums, match expression) |
| **MySQL / MariaDB** | — | Database |
| **Composer** | — | Dependency manager untuk PHP |
| **npm** | — | Asset building (jika ada JS assets) |

### 3.2 Library & Package (Composer)

| Package | Versi | Kegunaan |
|---------|-------|----------|
| `laravel/framework` | ^13.7 | Core framework |
| `laravel/tinker` | ^3.0 | REPL interaktif |
| `barryvdh/laravel-dompdf` | ^3.1 | Generate dokumen PDF (kwitansi) |
| `simplesoftwareio/simple-qrcode` | ^4.2 | Generate QR code SVG |
| `maatwebsite/excel` | ^3.1 | Export data ke Excel/CSV |
| `spatie/laravel-permission` | ^7.4 | Role-based access control (RBAC) |

### 3.3 Dev Dependencies

| Package | Kegunaan |
|---------|----------|
| `pestphp/pest` | Testing framework |
| `fakerphp/faker` | Generate fake data untuk testing |
| `laravel/pint` | Code formatter (PSR-12 + Laravel style) |
| `mockery/mockery` | Mocking untuk unit test |
| `laravel/pail` | Error logging |
| `laravel/pao` | Audit logging |

### 3.4 Frontend & UI

| Teknologi | Kegunaan |
|-----------|----------|
| **Blade Templates** | Template engine Laravel |
| **Tailwind CSS** | Styling (via Laravel Vite/asset pipeline) |
| **Alpine.js** | Interaktivitas UI (dropdown, modal, toggle) |
| **Heroicons / Lucide Icons** | Ikon SVG |
| **Chart.js** | Grafik statistik di dashboard |
| **DataTables (Yajra)** | Tabel interaktif dengan pagination, search, sort |

### 3.5 Database Schema

Berikut tabel-tabel yang ada di database:

```
users                    → Akun user/admin
roles                    → Role (admin, staf, dll)
permissions              → Permission
role_has_permissions     → Pivot role-permission
user_has_roles           → Pivot user-role
categories               → Kategori buku
books                    → Katalog buku
members                  → Anggota perpustakaan
borrowings               → Transaksi peminjaman
borrowing_details        → Detail buku yang dipinjam
book_returns             → Record pengembalian
fines                    → Denda keterlambatan
settings                 → Konfigurasi aplikasi
whatsapp_logs            → Log pengiriman WhatsApp
audit_logs               → Log perubahan data
hero_slides              → Slider halaman landing
cache / jobs             → Standard Laravel tables
```

### 3.6 API Internal

Aplikasi menyediakan REST API internal (prefix: `/api`, require auth):

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `GET` | `/api/members/lookup?code=` | Cari anggota via QR code |
| `GET` | `/api/books/lookup?code=` | Cari buku via QR code |
| `GET` | `/api/borrowings/{id}` | Detail peminjaman |
| `POST` | `/api/borrowings` | Buat peminjaman baru |
| `GET` | `/api/borrowings/{id}/receipt` | Ambil data kwitansi |
| `GET` | `/api/borrowings/{id}/receipt/pdf` | Download kwitansi PDF |
| `POST` | `/api/borrowings/{id}/remind` | Kirim reminder WhatsApp |
| `GET` | `/api/settings/borrowing` | Ambil pengaturan peminjaman |

API ini digunakan oleh QR scanner dan antarmuka admin modern.

### 3.7 Enums (PHP 8.1+)

Aplikasi mendefinisikan enum untuk type safety:

```php
App\Enums\BookStatus            → Available, Unavailable
App\Enums\MemberStatus          → Active, Inactive
App\Enums\BorrowingStatus       → Active, Returned, Late
App\Enums\BorrowingDetailStatus → Borrowed, Returned
App\Enums\FineStatus             → Unpaid, Paid
```

### 3.8 Middleware

| Middleware | Fungsi |
|------------|--------|
| `auth` | Memastikan user sudah login |
| `role` | Memastikan user memiliki role tertentu |
| `throttle` | Rate limiting untuk endpoint sensitif |
| Custom redirect middleware | Arahkan user sesuai status auth |

### 3.9 Helper Functions

| Function | Lokasi | Kegunaan |
|----------|--------|----------|
| `app_setting($key, $default)` | `app/Support/helpers.php` | Ambil nilai dari tabel `settings` |

---

## 4. Struktur Direktori Utama

```
perpustakaan-modern/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── LandingPageController.php
│   │   │   ├── Api/
│   │   │   │   └── BorrowingApiController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── BookController.php
│   │   │       ├── MemberController.php
│   │   │       ├── BorrowingController.php
│   │   │       ├── ReturnController.php
│   │   │       ├── FineController.php
│   │   │       ├── CategoryController.php
│   │   │       ├── UserController.php
│   │   │       ├── ReportController.php
│   │   │       ├── ExportController.php
│   │   │       ├── AuditLogController.php
│   │   │       ├��─ SettingController.php
│   │   │       ├── HeroSlideController.php
│   │   │       ├── QrScanController.php
│   │   │       └── WhatsAppSettingsController.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       ├── Books/
│   │       ├── Borrowings/
│   │       ├── Categories/
│   │       ├── Members/
│   │       ├── Users/
│   │       ├── HeroSlide/
│   │       ├── Settings/
│   │       └── WhatsApp/
│   ├── Models/
│   │   ├── User.php, Book.php, Member.php
│   │   ├── Borrowing.php, BorrowingDetail.php
│   │   ├── BookReturn.php, Fine.php
│   │   ├── Category.php, Setting.php
│   │   ├── AuditLog.php, WhatsAppLog.php
│   │   ├── HeroSlide.php
│   │   └── Role.php, Permission.php (Spatie)
│   ├── Enums/
│   ├── Services/
│   │   ├── AuditService.php
│   │   ├── BookService.php
│   │   ├── BorrowingService.php
│   │   ├── MemberQrCodeService.php
│   │   ├── MemberPhotoService.php
│   │   ├── ReceiptService.php
│   │   ├── WhatsAppService.php
│   │   └── WhatsAppSettingsService.php
│   └── Support/
│       └── helpers.php
├── config/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── landing/
│       ├── auth/
│       └── admin/
├── routes/
│   ├── web.php
│   └── api.php
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 5. Alur Data Utama

### 5.1 Alur Peminjaman Buku

```
Admin lookup Member
    ↓
Cek apakah anggota aktif & belum mencapai batas peminjaman (max 3)
    ↓
Admin lookup Book
    ↓
Cek apakah stok buku tersedia
    ↓
Set tanggal jatuh tempo (dari settings)
    ↓
Simpan Borrowing + BorrowingDetail
    ↓
Update stok buku (status unavailable jika semua unit dipinjam)
    ↓
Generate kwitansi
```

### 5.2 Alur Pengembalian Buku

```
Admin scan QR / pilih borrowing
    ↓
Tandai BorrowingDetail.status = Returned
    ↓
Hitung apakah terlambat → jika ya, buat Fine record
    ↓
Update stok buku (status available)
    ↓
Jika semua buku dikembalikan → Borrowing.status = Returned
    ↓
Generate kwitansi pengembalian
```

### 5.3 Alur WhatsApp Reminder

```
Admin klik "Kirim Reminder" di halaman peminjaman
    ↓
Cek konfigurasi WhatsApp API di settings
    ↓
WhatsAppService kirim request ke external API
    ↓
Simpan log ke whatsapp_logs
    ↓
Return status (success/failed)
```

---

## 6. Aturan Bisnis Penting

| Aturan | Nilai |
|--------|-------|
| Maksimal peminjaman per anggota | 3 buku |
| Durasi pinjam default | Diatur di settings |
| Tarif denda per hari | Diatur di settings |
| Status anggota default | Active |
| Stok buku per entry | 1 (dapat dikloning oleh admin) |
| QR Code format | SVG |

---

## 7. Perintah Artisan yang Sering Digunakan

```bash
# Development
php artisan serve                          # Jalankan dev server
php artisan tinker                         # REPL interaktif

# Generate
php artisan make:model Product -mfsc       # Model + migration + factory + seeder + controller
php artisan make:request StoreProductRequest
php artisan make:service ProductService

# Database
php artisan migrate                        # Jalankan migration
php artisan migrate:fresh --seed         # Reset + seed (HATI-HATI!)
php artisan db:seed --class=ProductSeeder

# Testing
php artisan test                           # Jalankan semua test
php artisan test --filter="product"        # Filter test spesifik

# Maintenance
php artisan pint                          # Format kode
php artisan route:list --name=api        # Lihat daftar API route
```

---

*Dokumen ini dibuat secara otomatis pada 17 Mei 2026.*
*Project: Perpustakaan Modern — Laravel 12*