<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class Order extends Model{
    protected $table = "orders";
    protected $fillable = [
        "contact_id","exam_id","items","mensajes","ncaja","npedidolab","lab_id","user_id",
        "observaciones","armazon_code","armazon_name","status"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
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
    protected static function booted(){
        static::created(function ($order) {
            $items = json_decode($order->items, true);
            $total = 0;

            foreach($items as $item){
                $total += $item['total'];
            }

            $sale['items'] = $order->items;
            $sale['subtotal'] = $total;
            $sale['total'] = $total;
            $sale['contact_id'] = $order->contact_id;
            $sale['order_id'] = $order->id;
            $sale['user_id'] = Auth::id();
            $sale['created_at'] = $order->created_at;
            $sale['updated_at'] = $order->updated_at;
            Sale::create($sale);

            /*
            $updateItem = StoreItem::find($item->store_items_id);
            $updateItem->cant += $item->amount;
            $updateItem->price = $item->price;
            $updateItem->save();
            */
        });
    }
}
