<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    //Scopes
    public function scopePublish($query)
    {
        $query->whereNull('deleted_at');
    }
}