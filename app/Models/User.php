<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use App\Models\Meta;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $table = "users";
    protected $fillable = [
        "name",
        "username",
        "email",
        "password",
        "rol",
        "branch_id"
    ];
    protected $hidden = [
        "password",
        "remember_token",
        "api_token",
        "deleted_at"
    ];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
    public function getAuthIdentifier()
    {
        return 1;
    }
    public function guardName()
    {
        return "api";
    }
    //Relationship
    public function session()
    {
        return $this->belongsTo('App\Models\Session', 'id', 'session_id');
    }
    public function branch()
    {
        return $this->belongsTo('App\Models\Config', 'branch_id', 'id');
    }
    public function metas()
    {
        return $this->morphMany(Meta::class, 'metable');
    }
    //Scopes
    public function scopeSearch($query, $search)
    {
        if (trim($search) != "") {
            $query->where(function ($q) use ($search) {
                $q->where('name', "LIKE", "%$search%")
                    ->orWhere('email', "LIKE", "$search%")
                    ->orWhere('username', "LIKE", "$search%");
            });
        }
    }
    public function scopeRole($query, $search)
    {
        if (trim($search) != "") {
            if ($search === "nodoctor") {
                return $query->whereHas('roles', function ($q) {
                    $q->where('roles.name', "admin")
                        ->orWhere('roles.name', "ventas");
                });
            }

            if (str_contains($search, ',')) {
                $roles = explode(",", $search);

                if (!count($roles)) return;

                return $query->whereHas('roles', function ($q) use ($roles) {
                    foreach ($roles as $key => $role) {
                        if (!$key) {
                            $q->where('roles.name', $role);
                            continue;
                        }

                        $q->orWhere('roles.name', $role);
                    }
                });
            }

            $query->whereHas('roles', function ($q) use ($search) {
                $q->where('roles.name', $search);
            });
        }
    }
    public function scopeUserName($query, $search, $userId = false)
    {
        if (trim($search) != "") {
            $query->where("username", "LIKE", $search);
            if ($userId) $query->where("id", "!=", $userId);
        }
    }
    public function scopeBranchId($query, $search)
    {
        $branch_id = trim($search);
        if (is_numeric($branch_id)) $branch_id = (int) $branch_id;
        else $branch_id = 0;

        if ((bool) $branch_id) {
            $query->where("branch_id", "=", $branch_id);
        }
    }
    public function scopeUserEmail($query, $search, $userId = false)
    {
        if (trim($search) != "") {
            $query->where("email", "LIKE", $search);
            if ($userId) $query->where("id", "!=", $userId);
        }
    }
    public function scopeNobot($query)
    {
        $query->where("id", "!=", 1);
    }
    public function scopePublish($query, $confirm = true)
    {
        if ($confirm) {
            $query->whereNull("deleted_at");
        }
    }
    public function scopeWithRelation($query)
    {
        $query->with('session', 'branch');
    }
    // Listerning 
    protected static function booted()
    {
        static::updated(function (User $user) {
            $type = "";
            $dirty = $user->getDirty();
            unset($dirty['updated_at']);
            unset($dirty['updated_id']);


            if (isset($dirty['api_token']) || !count($dirty)) {
                return null;
            }

            $data = ["user_id" => 1, "inputs" => $dirty];
            if (is_null($user->deleted_at)) {
                $data['datetime'] = Carbon::now();
                $type = "updated";
            } else {
                $data['datetime'] = Carbon::now();
                $type = "deleted";
            }

            $user->metas()->create(["key" => $type, "value" => $data]);
            Log::info("User data change:" . json_encode($user));
        });
    }
}
