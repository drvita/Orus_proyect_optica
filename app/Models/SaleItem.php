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
    public function branch()
    {
        return $this->belongsTo(Config::class, 'branch_id');
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
    public function scopeWithRelation($query)
    {
        $query->with('user', 'item', 'order', 'saleDetails', 'branch');
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
            if ($item->inBranch && count($item->inBranch)) {
                $branch_id = $item->branch_default ? $item->branch_default : $sale->branch_id;
                foreach ($item->inBranch as $branch) {
                    // If the same?
                    if ($branch->branch_id === $branch_id) {
                        // Only discount if we have cant

                        if ($sale->cant) {
                            $storeBranch = StoreBranch::where("id", $branch->id)->first();
                            if ($type === "created") {
                                $storeBranch->cant -= $sale->cant;
                            } else {
                                $storeBranch->cant += $sale->cant;
                            }
                            $storeBranch->save();
                        } else {
                            Log::error("The sales $item->session with item $item->code not have cant to download");
                        }

                        return [
                            "saleID" => $sale->id,
                            "itemId" => $item->id,
                            "name" => $item->name,
                            "code" => $item->code,
                            "branch" => $branch->branch_id,
                            "cant" => $sale->cant,
                            "status" => $sale->cant ? "OK" : "failer",
                            "message" => $sale->cant ? "" : "Cant no found o zero"
                        ];
                    }
                }

                Log::error("The item $item->code in sale $sale->id doesn't match with branches: $branch_id");
                return [
                    "saleID" => $sale->id,
                    "itemId" => $item->id,
                    "name" => $item->name,
                    "code" => $item->code,
                    "branch" => $branch_id,
                    "cant" => $sale->cant,
                    "status" => "failer",
                    "message" => "Item doesn't do match with branches"
                ];
            } else {
                // send notification because no exit in branch
                Log::error("The item $item->code doesn't have branches");
                $sale->sendErrorNotification($sale, $item);
                return [
                    "saleID" => $sale->id,
                    "itemId" => $item->id,
                    "name" => $item->name,
                    "code" => $item->code,
                    "branch" => $sale->branch_id,
                    "cant" => $sale->cant,
                    "status" => "failer",
                    "message" => "Item not have branches"
                ];
            }
        } else {
            // send notification because no exit item
            Log::error("The item $sale->store_item_id not found");
            $sale->sendErrorNotification($sale);
            return [
                "saleID" => $sale->id,
                "itemId" => $sale->store_item_id,
                "name" => "",
                "code" => "",
                "branch" => $sale->branch_id,
                "cant" => $sale->cant,
                "status" => "failer",
                "message" => "Item not found in store"
            ];
        }
    }
    //Listerner
    protected static function booted()
    {
        static::created(function ($sale) {
            $sale->processInStorageItem($sale, "created");
            // $result = $sale->processInStorageItem($sale, "created");
            // if ($result['status'] === "failer") {
            //     dd($result, $sale);
            // }
        });
        static::deleted(function ($sale) {
            $sale->processInStorageItem($sale, "deleted");
        });
    }
}