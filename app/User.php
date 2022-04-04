<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = "users";
    protected $fillable = [
        "name", "username", "email", "password", "rol", "api_token", "branch_id"
    ];
    protected $hidden = [
        "password", "remember_token", "api_token", "deleted_at"
    ];
    protected $dates = [
        'updated_at', 'created_at', 'deleted_at'
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
