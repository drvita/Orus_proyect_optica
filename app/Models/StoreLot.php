<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreLot extends Model{
    protected $table = "store_lots";
    protected $fillable = [
        "base64","bill","cost","price","amount","store_items_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function producto(){
        return $this->belongsTo('App\Models\StoreItem','store_items_id');
    }
}