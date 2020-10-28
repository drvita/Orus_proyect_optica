<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\type;

class User extends Model {
    protected $table = "users";
    protected $fillable = [
        "name","username","email","password","rol","api_token"
    ];
    protected $hidden = [
        "password","remember_token","api_token"
    ];
    protected $dates = [
        'updated_at','created_at'
    ];
    public function getAuthIdentifier(){
        return 1;
    }
    public function session(){
        return $this->belongsTo('App\Models\Session','id', 'session_id');
    }
    public function scopeSearch($query, $search){
        if(trim($search) != ""){
            $query->where(function($q) use($search){
                $q->where('name',"LIKE","%$search%")
                    ->orWhere('email',"LIKE","$search%")
                    ->orWhere('username',"LIKE","$search%");
            });
        }
    }
    public function scopeRol($query, $search){
        if(trim($search) != ""){
            $search = $search * 1;
            if($search >= 0 && $search <= 2) {
                $query->where("rol",$search);
            }
        }
    }
    public function scopeUserName($query, $search){
        if(trim($search) != ""){
            $query->where("username","LIKE",$search);
        }
    }
    public function scopeUserEmail($query, $search){
        if(trim($search) != ""){
            $query->where("email","LIKE",$search);
        }
    }

}
