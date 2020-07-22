<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model{

    protected $table = "contacts";
    protected $fillable = [
        "name","rfc","email","type","telnumbers","birthday","domicilio","user_id"
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
