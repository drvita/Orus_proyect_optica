<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
//use App\Models\Category;

class Category extends Model
{
    protected $table = "categories";
    protected $fillable = [
        "name",
        "category_id",
        "user_id"
    ];
    protected $hidden = ['updated_at', 'created_at', 'user_id', 'category_id'];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function parent()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    public function categories()
    {
        return $this->hasMany(Category::class, 'category_id', 'id');
    }
    public function sons()
    {
        return $this->hasMany(Category::class, 'category_id', 'id');
    }
    public function items()
    {
        return $this->hasMany(StoreItem::class, 'category_id', 'id');
    }

    // Attributes
    public function getRootAttribute()
    {
        return getRootCategory($this);
    }

    // scopes
    public function scopeCategoryId($query, $search)
    {
        if (trim($search) != "") {
            if ($search === "raiz") {
                $query->whereNull("category_id");
            } else {
                $query->where("category_id", $search);
            }
        }
    }
    public function scopeWithRelation($query)
    {
        $query->with('user', 'sons', 'parent.parent.parent');
    }
    public function scopeSearch($query, $search)
    {
        if (trim($search) != "") {
            $query->Where("name", "like", "$search%");
        }
    }

    // Other functions
    public function getCode()
    {
        return getShortNameCat($this->name);
    }
    public function getParentCategories()
    {
        return getParentCategories($this);
    }
    public function getMainCategory()
    {
        return getRootCategory($this, false) ?? $this;
    }
}
