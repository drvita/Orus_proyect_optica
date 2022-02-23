<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Listener;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\OrderSave::class => [
            \App\Listeners\OrderSave::class,
        ],
        \App\Events\PaymentSave::class => [
            \App\Listeners\PaymentSave::class,
        ],
        \App\Events\ExamEvent::class => [
            \App\Listeners\ExamListener::class,
        ],
        \App\Events\SaleSave::class => [
            \App\Listeners\SaleSave::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}