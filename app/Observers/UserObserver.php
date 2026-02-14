<?php

namespace App\Observers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    private function getNumberRole($user)
    {
        $role = $user->roles->first();
        switch ($role?->name) {
            case 'admin':
                return 0;
            case 'ventas':
                return 1;
            case 'doctor':
                return 2;
            default:
                return 1;
        }
    }
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        $user->rol = $this->getNumberRole($user);
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        if (is_null($user->rol)) {
            $user->rol = $this->getNumberRole($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $type = "";
        $dirty = $user->getDirty();
        unset($dirty['updated_at']);
        unset($dirty['updated_id']);
        unset($dirty['api_token']);

        if (!count($dirty)) {
            return;
        }

        $user_id = 1;
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        }

        $data = ["user_id" => $user_id, "inputs" => $dirty];
        if (is_null($user->deleted_at)) {
            $data['datetime'] = Carbon::now();
            $type = "updated";
        } else {
            $data['datetime'] = Carbon::now();
            $type = "deleted";
        }

        $user->metas()->create(["key" => $type, "value" => $data]);
        Log::info("[Observer.user] User data change", $data);
    }
}
