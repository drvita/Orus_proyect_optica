<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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

}
