<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;
use App\User;
use App\Models\Category;
use App\Models\Contact;
use App\Models\StoreLot;
use App\Models\Brand;
use App\Models\StoreBranch;

class StoreItem extends Model
{
    protected $table = "store_items";
    protected $fillable = [
        "code", "codebar", "grad", "brand_id", "name", "unit", "cant", "price", "category_id", "contact_id", "user_id", "branch_default"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
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
    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function lote()
    {
        return $this->hasMany(StoreLot::class, 'store_items_id');
    }
    public function salesItems()
    {
        return $this->belongsTo(SalesItems::class, 'store_items_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function inBranch()
    {
        return $this->hasMany(StoreBranch::class, 'store_item_id', 'id');
    }
    //Scopes
    public function scopeSearchItem($query, $search)
    {
        if (trim($search) != "") {
            $query->where(function ($q) use ($search) {
                $q->where('name', "LIKE", "%$search%")
                    ->orWhere('code', "LIKE", "$search%")
                    ->orWhere("codebar", "LIKE", $search);
            });
        }
    }
    public function scopeZero($query, $search)
    {
        if ($search == "true") {
            $query->where("cant", "<=", 0);
        }
    }
    public function scopeCategory($query, $val)
    {
        if ($val > 0) {
            $query->where("category_id", $val);
        }
    }
    public function scopeSearchCode($query, $search)
    {
        if (trim($search) != "") {
            $query->where("code", "LIKE", $search);
        }
    }
    public function scopeSearchSupplier($query, $search)
    {
        //$name = preg_replace('/\d+/', "", $name);

        if (trim($search) != "") {
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
        if (trim($search) != "") {
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
    public function scopePublish($query)
    {
        $query->whereNull('deleted_at');
    }
    public function scopeWithRelation($query)
    {
        $query->with('user', 'supplier', 'brand', 'inBranch');
    }
}