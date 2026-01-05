<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StoreItem;
use App\Models\User;
use Carbon\Carbon;

class StoreLot extends Model
{
    protected $table = "store_lots";
    protected $fillable = [
        "cost",
        "price",
        "cant",
        "num_invoice",
        "store_branch_id",
        "store_items_id",
        "user_id"
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
    public function producto()
    {
        return $this->belongsTo(StoreItem::class, 'store_items_id');
    }
    public function storeBranch()
    {
        return $this->belongsTo(StoreBranch::class, 'store_branch_id');
    }
    public function branch()
    {
        return $this->belongsTo(StoreBranch::class, 'store_branch_id');
    }
    //Scopes
    public function scopeBranch($query, $search)
    {
        if (trim($search) != "") {
            $query->where("branch_id", $search);
        }
    }
    //Functions
    protected static function booted()
    {
        static::created(function ($item) {
            $data = [
                "user_id" => $item->user_id,
                "inputs" => [
                    "branch_id" => $item->storeBranch ? $item->storeBranch->branch_id : "--",
                    "cant" => $item->cant,
                    "invoice" => $item->num_invoice,
                ],
                "datetime" => Carbon::now(),
            ];
            $item->producto->metas()->create(["key" => "created lot", "value" => $data]);
        });
        // static::deleted(function ($item) {
        //     $updateItem = StoreItem::find($item->store_items_id);
        //     $updateItem->cant -= $item->amount;
        //     $updateItem->save();
        // });
    }
}
