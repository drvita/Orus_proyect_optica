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
    public function scopeRol($query, $search)
    {
        if (trim($search) != "") {
            $search = $search * 1;
            if ($search >= 0 && $search <= 2) {
                $query->where("rol", $search);
            } else if ($search == 10) {
                $query->where("rol", "<=", 1);
            }
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
    public function scopeBot($query)
    {
        $query->where("id", "!=", 1);
    }
    public function scopeNotDelete($query, $confirm = true)
    {
        if ($confirm) {
            $query->whereNull("deleted_at");
        }
    }
}