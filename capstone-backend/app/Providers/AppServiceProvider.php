<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\PhReadingObserver;
use App\Models\Data;

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
        Data::observe(PhReadingObserver::class);
    }
}
