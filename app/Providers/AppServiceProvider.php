<?php

namespace App\Providers;

use App\Models\Quotation;
use App\Observers\QuotationObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Vite::prefetch(concurrency: 3);
        Quotation::observe(QuotationObserver::class);
    }
}
