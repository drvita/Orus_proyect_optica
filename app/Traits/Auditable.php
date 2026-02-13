<?php

namespace App\Traits;

use App\Models\Meta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            $user_id = $model->user_id ?? null;
            if (Auth::check()) {
                $user = Auth::user();
                $user_id = $user->id ?? $model->user_id ?? null;
            }
            $model->metas()->create([
                "key" => "created",
                "value" => [
                    "datetime" => $model->created_at,
                    "user_id" => $user_id,
                    "session" => [
                        "ip" => request()->ip(),
                        "browser" => request()->userAgent(),
                    ]
                ]
            ]);
            Log::info("[Auditable] " . class_basename($model) . " created: " . $model->id);
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            unset($changes['updated_id']);
            unset($changes['started_at']);
            unset($changes['ended_at']);
            unset($changes['created_at']);

            // Soft delete check
            $type = "updated";
            if (method_exists($model, 'trashed') && $model->wasChanged('deleted_at') && !is_null($model->deleted_at)) {
                $type = "deleted";
            }

            if (count($changes) > 0) {
                $user_id = $model->user_id ?? null;
                if (Auth::check()) {
                    $user = Auth::user();
                    $user_id = $user->id ?? $model->user_id ?? null;
                }
                $data = [
                    "user_id" => $user_id,
                    "inputs" => $changes,
                    "datetime" => $model->updated_at,
                    "session" => [
                        "ip" => request()->ip(),
                        "browser" => request()->userAgent(),
                    ]
                ];

                if ($type === "deleted") {
                    $data['datetime'] = $model->deleted_at;
                }

                $model->metas()->create([
                    "key" => $type,
                    "value" => $data
                ]);
            }
            Log::info("[Auditable] " . class_basename($model) . " updated: " . $model->id);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $user_id = $model->user_id ?? null;
                if (Auth::check()) {
                    $user = Auth::user();
                    $user_id = $user->id ?? $model->user_id ?? null;
                }
                $model->metas()->create([
                    "key" => "restored",
                    "value" => [
                        "user_id" => $user_id,
                        "datetime" => now(),
                        "session" => [
                            "ip" => request()->ip(),
                            "browser" => request()->userAgent(),
                        ]
                    ]
                ]);
                Log::info("[Auditable] " . class_basename($model) . " restored: " . $model->id);
            });
        }
    }

    public function getActivityAttribute()
    {
        $keys = property_exists($this, 'auditActivities')
            ? $this->auditActivities
            : ["updated", "deleted", "created", "restored"];

        return $this->metas()
            ->whereIn("key", $keys)
            ->orderBy("id", "desc")
            ->take(25)
            ->get();
    }

    public function metas()
    {
        return $this->morphMany(Meta::class, 'metable');
    }

    public function registerLogin()
    {
        $this->metas()->create([
            'key' => 'login',
            'value' => [
                'datetime' => Carbon::now(),
                'session' => [
                    "ip" => request()->ip(),
                    "browser" => request()->userAgent(),
                ]
            ]
        ]);
        Log::info("[Auditable] " . class_basename($this) . " logged in: " . ($this->email ?? $this->id));
    }
    public function registerLogout()
    {
        $this->metas()->create([
            'key' => 'logout',
            'value' => [
                'datetime' => Carbon::now(),
                'session' => [
                    "ip" => request()->ip(),
                    "browser" => request()->userAgent(),
                ]
            ]
        ]);
        Log::info("[Auditable] " . class_basename($this) . " logged out: " . ($this->email ?? $this->id));
    }
}
