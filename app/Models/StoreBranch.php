<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StoreBranch extends Model
{
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
    //Scopes
    public function scopePublish($query)
    {
        $query->whereNull('deleted_at');
    }
    // Listerning 
    protected static function booted()
    {
        parent::boot();

        static::created(function (StoreBranch $branch) {
            $auth = Auth::user();
            $data = [
                "user_id" => $auth->id,
                "inputs" => [
                    "branch_id" => $branch->branch_id,
                    "cant" => $branch->cant,
                    "price" => $branch->price,
                ],
                "datetime" => Carbon::now(),
            ];

            $item = StoreItem::where("id", $branch->store_item_id)->with("metas")->first();
            $item->metas()->create(["key" => "created branch", "value" => $data]);
        });

        static::updated(function (StoreBranch $branch) {
            $auth = Auth::user();
            $dirty = $branch->getDirty();
            unset($dirty['updated_at']);
            unset($dirty['updated_id']);

            if (!$dirty || !count($dirty)) return;

            $data = [
                "user_id" => $auth->id,
                "inputs" => $dirty,
                "datetime" => Carbon::now(),
            ];

            $item = StoreItem::where("id", $branch->store_item_id)->with("metas")->first();
            $item->metas()->create(["key" => "updated", "value" => $data]);
        });
    }
}
