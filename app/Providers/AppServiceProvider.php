<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        \Carbon\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'id', 'indonesian');
        \App\Models\Attendance::observe(\App\Observers\AttendanceObserver::class);

        // Use compact Tailwind pagination view throughout the app
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');
    }
}
