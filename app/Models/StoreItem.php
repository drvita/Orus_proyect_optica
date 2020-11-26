<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

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
    public function scopeSearchItem($query, $search){
        if(trim($search) != ""){
            $query->where(function($q) use($search){
                $q->where('name',"LIKE","%$search%")
                    ->orWhere('code',"LIKE","$search%");
            });
        }
    }
    public function scopeZero($query, $search){
        if($search == "true"){
            $query->where("cant",0);
        }
    }
}
