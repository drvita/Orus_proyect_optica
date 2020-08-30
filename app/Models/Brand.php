<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model{
    protected $table = "brands";
    protected $fillable = [
        "name","contact_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function proveedor(){
        return $this->belongsTo('App\Models\Contact','contact_id');
    }
}
