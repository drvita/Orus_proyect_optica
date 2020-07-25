<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model{
    protected $table = "sales";
    protected $fillable = [
        "session","items","metodopago","subtotal","descuento","anticipo","total","banco",
        "contact_id","order_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function cliente(){
        return $this->belongsTo('App\Models\Contact','contact_id');
    }
    public function pedido(){
        return $this->belongsTo('App\Models\Order','order_id');
    }
}
