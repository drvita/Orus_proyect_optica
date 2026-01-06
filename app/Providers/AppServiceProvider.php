<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        setlocale(LC_ALL, "es_MX.UTF-8");
        Carbon::setLocale('es_MX.UTF-8');
        date_default_timezone_set('America/Mexico_City');

        if (config('app.env') === "production") {
            DB::listen(function ($query) {
                if ($query->time > 1000) {
                    $path = request()->fullUrl();
                    $message = "[Slow query detected] " . $query->time . " ms";
                    $message .= " URL: " . $path;
                    $message .= " SQL Query: " . $query->sql;
                    $message .= " Bindings: " . implode(", ", $query->bindings);

                    Log::info($message);
                }
            });
        }
    }
}
