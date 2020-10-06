<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;

class Payment extends Model{
    protected $table = "payments";
    protected $fillable = [
        "metodopago","banco","auth","total","sale_id","contact_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function scopeSale($query, $search){
        if(trim($search) != ""){
            $query->where("sale_id",$search);
        }
    }

    protected static function booted(){
        static::created(function ($pay) {
            $updateSale = Sale::find($pay->sale_id);
            $updateSale->pagado += $pay->total;
            $updateSale->save();
        });
    }
}
