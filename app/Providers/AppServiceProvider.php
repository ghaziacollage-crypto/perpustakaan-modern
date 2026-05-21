<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.partials._aside', function ($view) {
            $pendingBorrowingsCount = Borrowing::where('status', BorrowingStatus::Pending)->count();
            $view->with('pendingBorrowingsCount', $pendingBorrowingsCount);
        });
    }
}
