<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;

class Order extends Model{
    protected $table = "orders";
    protected $fillable = [
        "contact_id","exam_id","ncaja","npedidolab","lab_id","user_id",
        "observaciones","session","status"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function scopeEstado($query, $search){
        if(trim($search) != ""){
            $query->where("status",$search);
        }
    }
    public function examen(){
        return $this->belongsTo('App\Models\Exam','exam_id');
    }
    public function paciente(){
        return $this->belongsTo('App\Models\Contact','contact_id');
    }
    public function laboratorio(){
        return $this->belongsTo('App\Models\Contact','lab_id');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function nota(){
        return $this->belongsTo('App\Models\Sale','id', 'order_id');
    }
    public function items(){
        return $this->hasMany('App\Models\SaleItem','session', 'session');
    }
    protected static function booted(){
        static::created(function ($item) {
        });
        static::deleted(function ($order) {
            $items = SaleItem::where('session', $order->session)->get();
            foreach($items as $item){
                SaleItem::find($item->id)->delete();
            }
        });
    }
}
