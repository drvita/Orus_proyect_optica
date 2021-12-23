<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;
use App\Models\StoreBranch;
use App\Notifications\ErrorStoreNotification;
use App\User;
use Illuminate\Support\Facades\Log;

// use Illuminate\Support\Facades\Auth;

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
        return $this->belongsTo(User::class, 'user_id');
    }
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'store_items_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'session', 'session');
    }
    public function saleDetails()
    {
        return $this->belongsTo(Sale::class, 'session', 'session');
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
    // Other functions
    public function sendErrorNotification($sale, $item)
    {
        User::where("id", "!=", 1)
            ->where("rol", 0)
            ->get()
            ->each(function (User $user) use ($sale, $item) {
                $user->notify(new ErrorStoreNotification($sale, $item));
            });
    }
    public function processInStorageItem($sale, $type = "created")
    {
        $item = StoreItem::where("id", $sale->store_items_id)->with('inBranch')->first();

        // Check is item exist
        if ($item) {
            // Check if we have items in branches
            if ($item->inBranch) {

                if ($item->branch_default) {
                    Log::debug(`Down item in store for branch default: {$item->branch_default}`);
                    foreach ($item->inBranch as $branch) {
                        // If the same?
                        if ($branch->branch_id === $item->branch_default) {
                            // Only discount if we have cant
                            if ($sale->cant) {
                                $storeBranch = StoreBranch::find($branch->id);
                                if ($type === "created") {
                                    $storeBranch->cant -= $sale->cant;
                                } else {
                                    $storeBranch->cant += $sale->cant;
                                }
                                $storeBranch->save();
                                return;
                            }
                            break;
                        }
                    }
                } else {
                    //Interate on all branches search the same the item
                    Log::debug(`Down item in store because not branch default: {$sale->branch_id}`);
                    foreach ($item->inBranch as $itemBranch) {
                        // If the same?
                        if ($itemBranch->branch_id === $sale->branch_id) {
                            // Only discount if we have cant
                            if ($sale->cant) {
                                $storeBranch = StoreBranch::find($itemBranch->id);

                                if ($type === "created") {
                                    $storeBranch->cant -= $sale->cant;
                                } else {
                                    $storeBranch->cant += $sale->cant;
                                }
                                $storeBranch->save();
                                return;
                            }
                            break;
                        }
                    }
                }
            } else {
                // send notification because no exit in branch
                Log::debug('Branch not exist');
                $sale->sendErrorNotification($sale, $item);
            }
        } else {
            // send notification because no exit item
            Log::debug('Item not exist');
            $sale->sendErrorNotification($sale);
        }
    }
    //Listerner
    protected static function booted()
    {
        static::created(function ($sale) {
            $sale->processInStorageItem($sale, "created");
        });
        static::deleted(function ($sale) {
            $sale->processInStorageItem($sale, "deleted");
        });
    }
}