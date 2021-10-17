<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;
use App\Models\StoreBranch;
use App\User;
use App\Models\Messenger;

class SaleItem extends Model
{
    protected $table = "sales_items";
    protected $fillable = [
        "cant", "price", "subtotal", "inStorage", "session", "store_items_id", "user_id", "out", "descripcion", "branch_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    //Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'store_items_id');
    }
    //Scopes
    public function scopeStock($query, $search)
    {
        if ($search != "") {
            $search = $search == "true" ? 1 : 0;
            $query->where("inStorage", $search)
                ->join('store_items', 'sales_items.store_items_id', '=', 'store_items.id')
                ->select('store_items.name as producto')
                ->selectRaw('SUM(sales_items.out) as faltante')
                ->selectRaw('SUM(sales_items.cant) as pedido')
                ->groupBy('store_items_id', 'name');
        }
    }
    public function scopeSaleDay($query, $date)
    {
        if ($date != "") {
            $query->select('session')
                ->WhereDate("created_at", $date)
                ->groupBy('session');
        }
    }
    //Listerner
    protected static function booted()
    {
        static::created(function ($item) {

            $itemInStore = StoreItem::where("id", $item->store_items_id)->with('inBranch')->first();

            // Check if we have items in branches
            if ($itemInStore->inBranch) {
                // dd("Si tiene branches:", $item->branch_id, $item->store_items_id, $itemInStore->toArray());
                //Interate on all branches search the same the item
                foreach ($itemInStore->inBranch as $itemBranch) {
                    // If the same?
                    if ($itemBranch->branch_id === $item->branch_id) {
                        // Only discount if we have cant
                        if ($item->cant) {
                            $storeBranch = StoreBranch::find($itemBranch->id);
                            $storeBranch->cant -= $item->cant;
                            $storeBranch->save();
                            return;
                        }
                        break;
                    }
                }
            } else {
                // send notification because no exit in branch
                Messenger::create([
                    "table" => "admins",
                    "idRow" => $item->id,
                    "message" => `Este no tiene entradas en alamacen`,
                    "user_id" => 1
                ]);
            }
        });
        static::deleted(function ($item) {
            $itemInStore = StoreItem::where("id", $item->store_items_id)->with('inBranch')->first();
            // Check if we have items in branches
            if ($itemInStore->inBranch) {
                //Interate on all branches search the same the item
                foreach ($itemInStore->inBranch as $itemBranch) {
                    // If the same?
                    if ($itemBranch->branch_id === $item->branch_id) {
                        // Only discount if we have cant
                        if ($item->cant) {
                            $storeBranch = StoreBranch::find($itemBranch->id);
                            $storeBranch->cant += $item->cant;
                            $storeBranch->save();
                            return;
                        }
                        break;
                    }
                }
            } else {
                // send notification because no exit in branch

            }
            // $updateItem = StoreItem::find($item->store_items_id);
            // Log::debug("Item eliminado, agregando producto a almacen");
            // if($item->cant){
            //     $updateItem->cant += $item->cant;
            //     $updateItem->save();
            // }
        });
    }
}