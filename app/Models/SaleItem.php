<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;
use Illuminate\Support\Facades\Log;

class SaleItem extends Model{
    protected $table = "sales_items";
    protected $fillable = [
        "cant","price","subtotal","inStorage","session","store_items_id","user_id","out","descripcion",
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
    public function scopeStock($query, $search){
        if($search != ""){
            $search = $search == "true" ? 1 : 0;
            $query->where("inStorage", $search)
                ->join('store_items', 'sales_items.store_items_id', '=', 'store_items.id')
                ->select('store_items.name as producto')
                ->selectRaw('SUM(sales_items.out) as faltante')
                ->selectRaw('SUM(sales_items.cant) as pedido')
                ->groupBy('store_items_id', 'name');
        }
    }
    public function scopeSaleDay($query, $date){
        if($date != ""){
            $query->select('session')
                ->WhereDate("created_at",$date)
                ->groupBy('session');
        }
    }
    protected static function booted(){
        static::created(function ($item) {
            $updateItem = StoreItem::find($item->store_items_id);
            Log::debug("Item creado, descontando producto de almacen");
            if($item->cant){
                $updateItem->cant -= $item->cant;
                $updateItem->save();
            }
        });
        static::deleted(function ($item) {
            $updateItem = StoreItem::find($item->store_items_id);
            Log::debug("Item eliminado, agregando producto a almacen");
            if($item->cant){
                $updateItem->cant += $item->cant;
                $updateItem->save();
            }
        });
    }
}