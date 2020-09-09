<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model{
    protected $table = "categories";
    protected $fillable = [
        "name","descripcion","category_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function parent(){
        return $this->belongsTo('App\Models\Category','category_id');
    }
    public function categories(){
        return $this->hasMany('App\Models\Category');
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
