<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model{
    protected $table = "store_items";
    protected $fillable = [
        "code","codebar","grad","brand","name","unit","cant","price","category_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function categoria(){
        return $this->belongsTo('App\Models\Category','category_id');
    }
    public function lote(){
        return $this->hasMany('App\Models\StoreLot','store_items_id');
    }
}
