<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        setlocale(LC_ALL, "es_MX.UTF-8");
        //Carbon::setLocale(config('app.locale'));
        Carbon::setLocale('es_MX.UTF-8');
    }
}
