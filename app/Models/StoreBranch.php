<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\StoreBranchObserver;
use App\Traits\Auditable;

#[ObservedBy([StoreBranchObserver::class])]
class StoreBranch extends Model
{
    use Auditable;
    protected $table = 'store_branches';
    protected $fillable = ["cant", "price", "store_item_id", "branch_id", "user_id", "updated_id"];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    // Relationship
    public function itemData()
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id', 'id');
    }
    public function branchData()
    {
        return $this->belongsTo(Config::class, 'branch_id', 'id');
    }
    public function lots()
    {
        return $this->hasMany(StoreLot::class, 'store_branch_id', 'id')->where("cant", ">", 0);
    }
    public function batches()
    {
        return $this->hasMany(StoreLot::class, 'store_branch_id', 'id');
    }

    //Scopes
    public function scopePublish($query)
    {
        $query->whereNull('deleted_at');
    }

    // Functions
    public function updateBatchesDecrement(int $cant): ?StoreLot
    {
        if ($cant <= 0) return null;

        $batches = $this->batches()
            ->where("cant", ">", 0)
            ->orderBy("created_at", "asc")
            ->get();

        $lastBatch = null;

        foreach ($batches as $batch) {
            if ($cant <= 0) break;

            $toTake = min($batch->cant, $cant);
            $batch->decrement('cant', $toTake);
            $cant -= $toTake;
            $lastBatch = $batch;
        }

        return $lastBatch;
    }
}
