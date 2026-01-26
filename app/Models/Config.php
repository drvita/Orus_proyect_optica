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

    // Attributes
    public function getValueAttribute(string $value)
    {
        try {
            if (is_string($value)) {
                $test = json_decode($value, true);
                if (is_null($test)) {
                    return $value;
                }
                return $test;
            }
            return $value;
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function scopeName($query, $search)
    {
        if (trim($search) === "") {
            $query->where("name", "LIKE", $search);
        }
    }
}
