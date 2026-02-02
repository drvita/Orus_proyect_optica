<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\UserObserver;
use App\Traits\Auditable;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use Auditable;

    protected $table = "users";
    const SOCIAL_CHANNELS = ['telegram', 'whatsapp'];
    protected $auditActivities = ["updated", "deleted", "created", "restored", "login"];
    protected $fillable = [
        "name",
        "username",
        "email",
        "password",
        "branch_id",
        "remember_token", // varchar 100
    ];
    protected $hidden = [
        "password",
        "remember_token",
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
        return $this->belongsTo(Session::class, 'id', 'session_id');
    }
    public function branch()
    {
        return $this->belongsTo(Config::class, 'branch_id', 'id');
    }
    public function phones()
    {
        return $this->morphMany(PhoneNumber::class, 'model');
    }

    // Attributes
    public function getLastSessionAttribute()
    {
        return $this->session;
    }
    public function getBranchNameAttribute()
    {
        return $this->branch->name;
    }
    public function getGenderAttribute()
    {
        return $this->metas()->where("key", "gender")->first()->value;
    }
    public function getNetworksAttribute()
    {
        return $this->metas()->whereIn("key", $this::SOCIAL_CHANNELS)->get();
    }

    // Scopes
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
}
