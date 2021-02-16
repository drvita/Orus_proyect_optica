<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Category;

class Category extends Model{
    protected $table = "categories";
    protected $fillable = [
        "name","category_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function parent(){
        return $this->belongsTo(Category::class,'category_id');
    }
    public function categories(){
        return $this->hasMany(Category::class);
    }
    public function scopeCategoryId($query, $search){
        if(trim($search) != ""){
            if($search === "raiz"){
                $query->where("category_id",null);
            } else {
                $query->where("category_id",$search);
            }
        }
    }
}
