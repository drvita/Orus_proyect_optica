<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class StoreItem extends Model
{
    use SoftDeletes, Auditable;
    protected $table = "store_items";
    protected $fillable = [
        "code",
        "codebar",
        "grad",
        "brand_id",
        "name",
        "unit",
        "cant",
        "price",
        "category_id",
        "contact_id",
        "user_id",
        "branch_default",
        "updated_id"
    ];
    protected $hidden = ["updated_id", "user_id"];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    //RelationsShip
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_id', 'id');
    }
    public function categoria()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function lote()
    {
        return $this->hasMany(StoreLot::class, 'store_items_id');
    }
    public function batches()
    {
        return $this->hasMany(StoreLot::class, 'store_items_id');
    }
    public function salesItems()
    {
        return $this->belongsTo(SaleItem::class, 'store_items_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function inBranch()
    {
        return $this->hasMany(StoreBranch::class, 'store_item_id', 'id');
    }

    // Scopes
    public function scopeSearchItem($query, $search)
    {
        if (trim($search) != "") {
            $query->where('name', "LIKE", "%$search%")
                ->orWhere('code', "LIKE", "$search%")
                ->orWhere("codebar", "LIKE", $search);
        }
    }
    public function scopeZero($query, $search)
    {
        if (!is_null($search) && $search == "true") {
            $query->whereHas('inBranch', function ($query) {
                $query->where('cant', "<=", 0);
            });
        }
    }
    public function scopeCategory($query, $id)
    {
        if (!is_null($id) && is_numeric($id) && $id) {
            $query->where("category_id", $id);
        }
    }
    public function scopeSearchCode($query, $search, int | null $id = 0)
    {
        if (trim($search) != "") {
            $query->where("code", "LIKE", $search)
                ->orWhere("codebar", "LIKE", $search);

            if ($id) {
                $query->where("id", "!=", $id);
            }
        }
    }
    public function scopeSearchSupplier($query, $search)
    {
        if (!is_null($search) && trim($search) != "") {
            $contact_id = 0;
            preg_match_all('!\d+!', $search, $matches);
            if (count($matches[0])) {
                $contact_id = (int) $matches[0][0];
            }

            if ($contact_id) {
                $query->Where('contact_id', $contact_id);
            } else {
                $query->whereHas('supplier', function ($query) use ($search) {
                    $query->where('name', "LIKE", "%$search%");
                });
            }
        }
    }
    public function scopeSearchBrand($query, $search)
    {
        if (!is_null($search) && trim($search) != "") {
            $brand_id = 0;
            preg_match_all('!\d+!', $search, $matches);
            if (count($matches[0])) {
                $brand_id = (int) $matches[0][0];
            }

            if ($brand_id) {
                $query->Where('brand_id', $brand_id);
            } else {
                $query->whereHas('brand', function ($query) use ($search) {
                    $query->where('name', "LIKE", "%$search%");
                });
            }
        }
    }
    public function scopeFilterBranch($query, int | null $id)
    {
        if (!is_null($id) && is_numeric($id)) {
            $query->whereHas('inBranch', function ($query) use ($id) {
                $query->where('branch_id', $id);
            });
        }
    }
    public function scopeWithRelations($query)
    {
        $query->with('user', 'user_updated', 'categoria', 'supplier', 'brand', 'inBranch.lots', 'metas');
    }
    public function scopeUpdateDate($query, $search)
    {
        if (!is_null($search) && trim($search) != "") {
            $query->whereDate('updated_at', "=", $search);
        }
    }
}
