<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;
    protected $fillable = ["key", "value"];
    public $timestamps = false;

    public function metable()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'value' => 'array'
    ];

    public function setPropertiesAttribute($value)
    {
        $properties = [];
        foreach ($value as $key => $array_item) {
            if (!is_null($array_item)) {
                $properties[$key] = $array_item;
            }
        }

        $this->attributes['value'] = json_encode($properties);
    }
    public function getValueAttribute($value)
    {
        $value = json_decode($value, true);

        if (is_string($value)) {
            $value = json_decode($value, true);
        }
        return $value;
    }
}
