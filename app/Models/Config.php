<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = "config";
    public $timestamps = false;
    protected $fillable = [
        "name",
        "value"
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function scopeName($query, $search)
    {
        if (trim($search) === "") {
            $query->where("name", "LIKE", $search);
        }
    }
}
