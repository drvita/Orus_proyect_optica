<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Contact;
use App\Observers\ContactObserver;
use Carbon\Carbon;

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
        setlocale(LC_ALL, "es_MX.UTF-8");
        //Carbon::setLocale(config('app.locale'));
        Carbon::setLocale('es_MX.UTF-8');
        date_default_timezone_set('America/Mexico_City');
        Contact::observe(ContactObserver::class);
    }
}
