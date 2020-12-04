<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model{
    protected $table = "sales";
    protected $fillable = [
        "subtotal","descuento","total","pagado","contact_id","order_id","user_id","session"
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
    public function items(){
        return $this->hasMany('App\Models\SaleItem','session', 'session');
    }
    public function abonos(){
        return $this->hasMany('App\Models\Payment','sale_id', 'id')->selectRaw('SUM(total) as suma');
    }

    public function scopeCliente($query, $name){
        $name = preg_replace('/\d+/', "", $name);
        if(trim($name) != "" && is_string($name)){
            $query->whereHas('cliente', function($query) use ($name){
                $query->where('name',"LIKE","%$name%");
            });
        }
    }
    public function scopeSearchId($query, $search){
        if(trim($search) != "" ){
            $search = (int) $search;
            if(is_numeric($search) && $search > 0){
                $query->Where("id",$search);
            }
        }
    }
    public function scopeType($query, $search){
        if(trim($search) != ""){
            settype($search, "boolean");
            if($search)
                $query->whereColumn("pagado","total");
            else
                $query->whereColumn("pagado","<","total");
        }
    }
    public function scopeDate($query, $search){
        if(trim($search) != ""){
            $query->WhereDate("created_at",$search);
        }
    }
}
