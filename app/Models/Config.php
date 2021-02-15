<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = "config";
    public $timestamps = false;
    protected $fillable = [
        "name","value"
    ];
    public function scopeName($query, $search){
        if(trim($search) === "") $search="bank";
        $query->where("name","LIKE",$search);
    }
}
