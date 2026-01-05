<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;
use App\Notifications\ErrorStoreNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SaleItem extends Model
{
    protected $table = "sales_items";
    protected $fillable = [
        "cant",
        "price",
        "subtotal",
        "inStorage",
        "session",
        "store_items_id",
        "store_branch_id",
        "store_lot_id",
        "user_id",
        "out",
        "descripcion",
        "branch_id"
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
    public function branchItem()
    {
        return $this->hasOne(StoreBranch::class, 'id', 'store_branch_id');
    }
    public function lot()
    {
        return $this->hasOne(StoreLot::class, 'id', 'store_lot_id');
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
            ->role("admin")
            ->get()
            ->each(function (User $user) use ($sale, $item) {
                $user->notify(new ErrorStoreNotification($sale, $item));
            });
    }
    public function processInStoreItem($saleitem, $type = "created")
    {
        $item = StoreItem::where("id", $saleitem->store_items_id)->with('inBranch')->first();
        $auth = Auth::user();
        $branch = $saleitem->branchItem;

        if ($branch) {
            $lot = $saleitem->lot;
            $cant = $saleitem->cant;
            $lot_selected = null;
            $typeSale = "";

            if ($type === "created") {
                $branch->cant -= $cant;
                $typeSale = "created item";

                if ($lot) {
                    $lot->cant -= $cant;
                    $lot->save();
                    $lot_selected = $lot->id;
                }
            } else {
                $branch->cant += $cant;
                $typeSale = "deleted item";

                if ($lot) {
                    $lot->cant += $cant;
                    $lot->save();
                    $lot_selected = $lot->id;
                }
            }

            if ($branch->cant < 0) {
                $branch->cant = 0;
            }

            $branch->updated_at = Carbon::now();
            $branch->updated_id = $auth->id;
            $branch->save();

            $sale = Sale::where("session", $saleitem->session)->first();
            if ($sale) {
                $dataSale = ["user_id" => $auth->id, "datetime" => Carbon::now()];
                $dataSale["inputs"] = [
                    "cant" => $saleitem->cant,
                    "branch_id" => $saleitem->branch_id,
                    "name" => $item->name,
                    "lot" => $lot_selected ?? "--",
                ];
                $sale->metas()->create(["key" => $typeSale, "value" => $dataSale]);
            }

            return [
                "saleID" => $saleitem->id,
                "itemId" => $item->id,
                "name" => $item->name,
                "code" => $item->code,
                "branch" => $branch->id,
                "cant" => $saleitem->cant,
                "status" => "ok",
            ];
        } else {
            Log::error("The item $item->code doesn't have branches");
            return [
                "saleID" => $saleitem->id,
                "itemId" => $item->id,
                "name" => $item->name,
                "code" => $item->code,
                "branch" => $saleitem->branch_id,
                "cant" => $saleitem->cant,
                "status" => "failer",
                "message" => "Item not have branches"
            ];
        }
    }
    //Listerner
    protected static function booted()
    {
        parent::boot();

        static::created(function (Saleitem $saleitem) {
            $saleitem->processInStoreItem($saleitem, "created");
        });
        static::deleted(function (Saleitem $saleitem) {
            $saleitem->processInStoreItem($saleitem, "deleted");
        });
    }
}
