<?php

namespace App\Listeners;

use App\Notifications\ExamNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ExamListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $notifyto = $event->rol === 1 ? 2 : 1;
        User::where("id", "!=", 1)
            ->where("id", "!=", Auth::user()->id)
            ->where("rol", $notifyto)
            ->each(function (User $user) use ($event) {
                //$user->notify(new ExamNotification($event->exam));
                Notification::send($user, new ExamNotification($event->exam));
            });
    }
}
