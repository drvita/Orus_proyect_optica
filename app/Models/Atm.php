<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atm extends Model{
    protected $table = "atms";
    protected $fillable = [
        "efectivo","tarjetas","cheques","venta","session_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
}
