<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;
use App\Models\StoreBranch;
use App\Notifications\ErrorStoreNotification;
use App\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            ->hasRole("admin")
            ->get()
            ->each(function (User $user) use ($sale, $item) {
                $user->notify(new ErrorStoreNotification($sale, $item));
            });
    }
    public function processInStoreItem($sale, $type = "created")
    {
        $item = StoreItem::where("id", $sale->store_items_id)->with('inBranch')->first();
        $auth = Auth::user();

        if ($item) {
            if ($item->inBranch && count($item->inBranch)) {
                foreach ($item->inBranch as $branch) {
                    if ($branch->branch_id === $sale->branch_id) {
                        if ($sale->cant) {
                            // $storeBranch = StoreBranch::where("id", $branch->id)->first();
                            if ($type === "created") {
                                $branch->cant -= $sale->cant;
                            } else {
                                $branch->cant += $sale->cant;
                            }

                            if ($branch->cant < 0) {
                                $branch->cant = 0;
                            }

                            $branch->updated_at = Carbon::now();
                            $branch->user_id = $auth->id;
                            $branch->save();
                        } else {
                            Log::error("The sales $item->session with item $item->code not have cant to rest");
                        }

                        $item->updated_at = Carbon::now();
                        $item->user_id = $auth->id;
                        $item->save();

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

                Log::error("The item $item->code in sale $sale->id doesn't match with branches: $sale->branch_id");
                return [
                    "saleID" => $sale->id,
                    "itemId" => $item->id,
                    "name" => $item->name,
                    "code" => $item->code,
                    "branch" => $sale->branch_id,
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
        parent::boot();

        static::created(function ($sale) {
            $sale->processInStoreItem($sale, "created");
        });
        static::deleting(function ($sale) {
            $sale->processInStoreItem($sale, "deleted");
        });
    }
}
