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
    public function scopeSearch($query, $search){
        if(trim($search) != ""){
            $query->where(function($q) use($search){
                $q->where('name',"LIKE","%$search%")
                    ->orWhere('email',"LIKE","$search%")
                    ->orWhere('username',"LIKE","%$search%");
            });
        }
    }
    public function scopeRol($query, $search){
        if(trim($search) != ""){
            $search = $search * 1;
            if($search >= 0 && $search <= 2) {
                //dd($search);
                $query->where("rol",$search);
            }
        }
    }

}
