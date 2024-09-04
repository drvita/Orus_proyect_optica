<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
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
        Carbon::setLocale('es_MX.UTF-8');
        date_default_timezone_set('America/Mexico_City');

        if (config('app.env') != "local") {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    $message = "Slow query detected:\n";
                    $message .= "Execution time: {$query->time} ms\n";
                    $message .= "SQL Query: {$query->sql}\n";
                    $message .= "Bindings: " . implode(", ", $query->bindings) . "\n";

                    // Log::debug($message);
                    \Sentry\captureMessage($message);
                }
            });
        }
    }
}
