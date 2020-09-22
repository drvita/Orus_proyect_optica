<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;

class SaleItem extends Model{
    protected $table = "sales_items";
    protected $fillable = [
        "cant","price","subtotal","inStorage","session","store_items_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function item(){
        return $this->belongsTo('App\Models\StoreItem','store_items_id');
    }

    protected static function booted(){
        static::created(function ($sale) {
            
        });
    }
}
