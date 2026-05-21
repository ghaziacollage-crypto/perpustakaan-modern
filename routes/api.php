<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BorrowingApiController;
use App\Http\Controllers\Api\ScanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Borrowing System (Auth Required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // ── Member Lookups ──────────────────────────────────────────────────────
    Route::get('/members/lookup', [BorrowingApiController::class, 'lookupMember'])
        ->name('api.members.lookup');

    // ── Book Lookups ────────────────────────────────────────────────────────
    Route::get('/books/lookup', [BorrowingApiController::class, 'lookupBook'])
        ->name('api.books.lookup');

    // ── Borrowing CRUD ───────────────────────────────────────────────────────
    Route::get('/borrowings/{borrowing}', [BorrowingApiController::class, 'show'])
        ->name('api.borrowings.show');
    Route::post('/borrowings', [BorrowingApiController::class, 'store'])
        ->name('api.borrowings.store');
    Route::post('/borrowings/{borrowing}/remind', [BorrowingApiController::class, 'remind'])
        ->name('api.borrowings.remind');

    // ── Receipt ────────────────────────────────────────────────────────────
    Route::get('/borrowings/{borrowing}/receipt', [BorrowingApiController::class, 'receipt'])
        ->name('api.borrowings.receipt');
    Route::get('/borrowings/{borrowing}/receipt/pdf', [BorrowingApiController::class, 'receiptPdf'])
        ->name('api.borrowings.receipt.pdf');

    // ── Settings ────────────────────────────────────────────────────────────
    Route::get('/settings/borrowing', [BorrowingApiController::class, 'settings'])
        ->name('api.settings.borrowing');

    // ── Admin: Approve Pending Borrowings ──────────────────────────────────
    Route::post('/borrowings/{borrowing}/approve', [BorrowingApiController::class, 'approve'])
        ->name('api.borrowings.approve');
    Route::post('/borrowings/{borrowing}/reject', [BorrowingApiController::class, 'reject'])
        ->name('api.borrowings.reject');
});

/*
|--------------------------------------------------------------------------
| Public API Routes — Kiosk & Return Scan (No Auth Required)
|--------------------------------------------------------------------------
| Used by the library kiosk/tablet scanner and admin return scan page
| No auth required — physical QR scanning at counter
*/
Route::prefix('scan')->withoutMiddleware(['auth', 'verified'])->group(function () {
    Route::post('/member', [ScanController::class, 'scanMember'])
        ->name('api.scan.member');
    Route::post('/book', [ScanController::class, 'scanBook'])
        ->name('api.scan.book');
    Route::get('/current-member', [ScanController::class, 'getCurrentMember'])
        ->name('api.scan.current-member');
    Route::delete('/book/{bookId}', [ScanController::class, 'removeBook'])
        ->name('api.scan.remove-book');
    Route::get('/queue', [ScanController::class, 'getQueue'])
        ->name('api.scan.queue');
});