<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Session extends Model{
    protected $table = "sessions";
    protected $fillable = [
        "session_id","ip_address","user_agent","last_activity","user_data"
    ];
    protected $hidden = [];
    protected $dates = [];
}
