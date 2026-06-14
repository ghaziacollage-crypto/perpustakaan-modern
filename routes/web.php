<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookReportController;
use App\Http\Controllers\Admin\BorrowingController;
use App\Http\Controllers\Admin\BorrowingLookupController;
use App\Http\Controllers\Admin\BorrowingReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FineController;
use App\Http\Controllers\Admin\FineReportController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\MemberReportController;
use App\Http\Controllers\Admin\QrScanController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WhatsAppSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Public Landing Page ──
Route::get('/', [LandingPageController::class, 'index'])->name('landing.home');
Route::get('/books', [LandingPageController::class, 'books'])->name('landing.books');
Route::get('/categories', [LandingPageController::class, 'categories'])->name('landing.categories');
Route::get('/books/{book}', [LandingPageController::class, 'showBook'])->name('landing.books.show');
Route::get('/member', [MemberController::class, 'index'])->name('member.index');
Route::get('/member/register', [MemberController::class, 'showRegisterForm'])->name('member.register');
Route::post('/member/register', [MemberController::class, 'register'])->name('member.register.store');
Route::get('/member/lookup', [MemberController::class, 'lookup'])->name('member.lookup');
Route::get('/member/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');
Route::get('/member/books', [MemberController::class, 'selectBook'])->name('member.books');
Route::get('/member/borrowings', [MemberController::class, 'myBorrowings'])->name('member.borrowings');
Route::post('/member/borrow', [MemberController::class, 'requestBorrow'])->name('member.borrow');
Route::post('/member/borrow/{id}/cancel', [MemberController::class, 'cancelBorrow'])->name('member.borrow.cancel');
Route::get('/member/borrowings/{id}/return-qr', [MemberController::class, 'returnQr'])->name('member.return-qr');

Route::get('/login-redirect', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
})->name('home');

// ── Kiosk Scan Page (perpustakaan counter tablet) ──
Route::get('/scan', [ScanController::class, 'index'])->name('scan.kiosk');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('books', BookController::class)->except(['edit']);
    Route::get('books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::get('books/create', [BookController::class, 'create'])->name('books.create');
    Route::get('books/{book}/qr-code', [BookController::class, 'showQrCode'])->name('books.qr-code');
    Route::get('books/{book}/qr-modal', [BookController::class, 'qrModal'])->name('books.qr-modal');
    Route::post('books/{book}/qr-code', [BookController::class, 'regenerateQrCode'])->name('books.regenerate-qr');
    Route::post('books/bulk-qr', [BookController::class, 'bulkGenerateQr'])->name('books.bulk-qr');
    Route::get('books/bulk-qr/print', [BookController::class, 'bulkPrintQr'])->name('books.bulk-qr.print');
    Route::get('books/lookup', [BookController::class, 'lookupByCode'])->name('books.lookup');
    Route::resource('members', AdminMemberController::class)->except(['create', 'edit', 'show']);
    Route::get('members/{member}', [AdminMemberController::class, 'show'])->name('members.show');
    Route::get('members/{member}/print', [AdminMemberController::class, 'printCard'])->name('members.print-card');
    Route::post('members/{member}/qr-code', [AdminMemberController::class, 'regenerateQr'])->name('members.regenerate-qr');
    Route::get('members/bulk-qr', [AdminMemberController::class, 'bulkQrPage'])->name('members.bulk-qr');
    Route::post('members/bulk-qr-regenerate', [AdminMemberController::class, 'bulkRegenerateQr'])->name('members.bulk-qr-regenerate');
    Route::post('members/{member}/approve', [AdminMemberController::class, 'approve'])->name('members.approve');
    Route::post('members/{member}/reject', [AdminMemberController::class, 'reject'])->name('members.reject');
    Route::get('members/lookup', [AdminMemberController::class, 'lookupByCode'])->name('members.lookup');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('users', UserController::class);

    Route::get('fines', [FineController::class, 'index'])->name('fines.index');

    Route::get('borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::post('borrowings/{borrowing}/approve', [BorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::post('borrowings/{borrowing}/reject', [BorrowingController::class, 'reject'])->name('borrowings.reject');
    Route::post('borrowings/{borrowing}/remind', [BorrowingController::class, 'remind'])->name('borrowings.remind');
    Route::get('borrowings/{borrowing}/receipt', [BorrowingController::class, 'receipt'])->name('borrowings.receipt');
    Route::get('borrowings/{borrowing}/receipt/pdf', [BorrowingController::class, 'receiptPdf'])->name('borrowings.receipt.pdf');

    // ── Lookup Routes (for QR scanning) — NO AUTH MIDDLEWARE ──
    Route::get('borrowings/lookup-member', [BorrowingLookupController::class, 'lookupMember'])
        ->withoutMiddleware(['auth', 'verified']);
    Route::get('borrowings/lookup-book', [BorrowingLookupController::class, 'lookupBook'])
        ->withoutMiddleware(['auth', 'verified']);
    Route::get('borrowings/lookup-by-code', [BorrowingLookupController::class, 'lookupByTransactionCode'])
        ->withoutMiddleware(['auth', 'verified']);

    Route::get('scan', [QrScanController::class, 'index'])->name('scan.index');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Fine report
    Route::get('reports/fines', [FineReportController::class, 'index'])->name('reports.fines.index');
    Route::get('reports/fines/export-pdf', [FineReportController::class, 'exportPdf'])->name('reports.fines.pdf');
    Route::get('reports/fines/by-member', [FineReportController::class, 'byMember'])->name('reports.fines.by-member');
    Route::get('reports/fines/by-member/{member}/pdf', [FineReportController::class, 'pdfByMember'])->name('reports.fines.by-member-pdf');

    // Book report
    Route::get('reports/books', [BookReportController::class, 'index'])->name('reports.books.index');
    Route::get('reports/books/export-pdf', [BookReportController::class, 'exportPdf'])->name('reports.books.pdf');

    // Borrowing report
    Route::get('reports/borrowings', [BorrowingReportController::class, 'index'])->name('reports.borrowings.index');
    Route::get('reports/borrowings/export-pdf', [BorrowingReportController::class, 'exportPdf'])->name('reports.borrowings.pdf');

    // Member report
    Route::get('reports/members', [MemberReportController::class, 'index'])->name('reports.members.index');
    Route::get('reports/members/export-pdf', [MemberReportController::class, 'exportPdf'])->name('reports.members.pdf');

    Route::get('export/books', [ExportController::class, 'books'])->name('export.books');
    Route::get('export/members', [ExportController::class, 'members'])->name('export.members');
    Route::get('export/borrowings', [ExportController::class, 'borrowings'])->name('export.borrowings');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    Route::get('returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('returns/scan', [ReturnController::class, 'scanReturn'])->name('returns.scan');
    Route::post('returns/{borrowing}', [ReturnController::class, 'store'])->name('returns.store');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('settings/whatsapp', [WhatsAppSettingsController::class, 'index'])->name('settings.whatsapp');
    Route::put('settings/whatsapp', [WhatsAppSettingsController::class, 'update'])->name('settings.whatsapp.update');
    Route::post('settings/whatsapp/test', [WhatsAppSettingsController::class, 'test'])->name('settings.whatsapp.test');

    Route::resource('hero-slides', HeroSlideController::class)->except(['create', 'edit', 'show']);
    Route::post('hero-slides/{heroSlide}/toggle', [HeroSlideController::class, 'toggle'])->name('hero-slides.toggle');
});
