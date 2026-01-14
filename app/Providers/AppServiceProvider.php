<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        setlocale(LC_ALL, "es_MX.UTF-8");
        Carbon::setLocale('es_MX.UTF-8');
        date_default_timezone_set('America/Mexico_City');

        if (config('app.env') === "production") {
            DB::listen(function ($query) {
                if ($query->time > 500) {
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
