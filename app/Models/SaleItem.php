<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;
use App\Notifications\ErrorStoreNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\SaleItemObserver;
use Illuminate\Support\Facades\Log;

#[ObservedBy([SaleItemObserver::class])]
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
    public function batch()
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
    /**
     * Inventory write-off process for a sale item.
     * 
     * This method attempts to deduct the requested quantity from inventory following this priority:
     * 1. Deduct from the same branch (branch_id) associated with the sale item.
     * 2. If insufficient, search and deduct the remaining balance from other branches with available stock.
     * 3. If stock is still missing, record the deficit in the 'out' field and set 'inStorage' to 0.
     * 
     * The 'store_branch_id' field is updated to reference the last branch from which stock was taken.
     *
     * @return void
     */
    public function writeOffProcess()
    {
        $branchId = $this->branch_id;
        if (!$branchId) {
            Log::error("[SaleItem::writeOffProcess] Branch ID not found for sale item: " . $this->id);
            return;
        }

        $item = $this->item;
        if (!$item) {
            Log::error("[SaleItem::writeOffProcess] Item not found for sale item: " . $this->id);
            return;
        }

        $needed = $this->cant;
        $totalProcessed = 0;
        $lastStoreBranchId = $branchId;

        // 1. Prioridad: Misma sucursal (Local)
        $localStoreBranch = $item->inBranch()->where('branch_id', $branchId)->first();
        if ($localStoreBranch && $localStoreBranch->cant > 0) {
            $toTake = min($localStoreBranch->cant, $needed);
            $localStoreBranch->decrement('cant', $toTake);

            // Descontar de lotes si existen
            $lot = $localStoreBranch->updateBatchesDecrement($toTake);
            if ($lot) {
                $this->store_lot_id = $lot->id;
                if ($lot->num_invoice) {
                    $this->descripcion = ($this->descripcion ? $this->descripcion . " | " : "") . "Fact: " . $lot->num_invoice;
                }
            }

            $totalProcessed += $toTake;
            $lastStoreBranchId = $localStoreBranch->id;
        }

        // 2. Fallback: Otras sucursales
        if ($totalProcessed < $needed) {
            $otherStoreBranches = $item->inBranch()
                ->where('branch_id', '!=', $branchId)
                ->where('cant', '>', 0)
                ->get();

            foreach ($otherStoreBranches as $otherStoreBranch) {
                $remains = $needed - $totalProcessed;
                $toTake = min($otherStoreBranch->cant, $remains);
                $otherStoreBranch->decrement('cant', $toTake);

                // Descontar de lotes si existen
                $lot = $otherStoreBranch->updateBatchesDecrement($toTake);
                if ($lot) {
                    $this->store_lot_id = $lot->id;
                    if ($lot->num_invoice) {
                        $this->descripcion = ($this->descripcion ? $this->descripcion . " | " : "") . "Fact: " . $lot->num_invoice;
                    }
                }

                $totalProcessed += $toTake;
                $lastStoreBranchId = $otherStoreBranch->id;

                if ($totalProcessed >= $needed) break;
            }
        }

        // 3. Resultado y Faltantes
        $missing = $needed - $totalProcessed;
        $this->out = $missing;
        $this->inStorage = ($missing == 0) ? 1 : 0;
        $this->store_branch_id = $lastStoreBranchId;

        if ($this->isDirty(['out', 'inStorage', 'store_branch_id', 'store_lot_id', 'descripcion'])) {
            $this->save();
        }

        if ($missing > 0) {
            Log::warning("[SaleItem::writeOffProcess] Stock insuficiente para item {$item->code}. Faltaron {$missing}");
        }
    }
}
