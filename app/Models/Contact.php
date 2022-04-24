<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Exam;
use App\Models\Brand;
use Carbon\Carbon;

class Contact extends Model
{
    protected $table = "contacts";
    //type 0 is customer contact  //business is a company : 1
    //Type 1 is supply contact    //Business not is company: 0,
    protected $fillable = [
        "name", "rfc", "email", "type", "telnumbers", "birthday", "domicilio", "user_id", "business", "updated_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'birthday'
    ];
    protected $casts = [
        'telnumbers' => 'array',
        'domicilio' => 'array',
    ];


    //Relations 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_id', 'id');
    }
    public function buys()
    {
        return $this->hasMany(Sale::class, 'contact_id', 'id')->orderBy('created_at', 'DESC');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'contact_id', 'id')->orderBy('created_at', 'DESC');
    }
    public function supplier()
    {
        return $this->hasMany(Order::class, 'lab_id', 'id')->orderBy('created_at', 'DESC');
    }
    public function exams()
    {
        return $this->hasMany(Exam::class, 'contact_id', 'id')->orderBy('updated_at', 'DESC');
    }
    public function brands()
    {
        return $this->hasMany(Brand::class, 'contact_id', 'id')->orderBy('updated_at', 'DESC');
    }
    public function metas()
    {
        return $this->morphMany(Meta::class, 'metable');
    }
    //Scopes
    public function scopeSearchUser($query, $search)
    {
        if (trim($search) != "") {
            $query->where('name', "LIKE", "%$search%")
                ->orWhere('email', "LIKE", "$search%")
                ->orWhere('rfc', "LIKE", "$search%");
        }
    }
    public function scopeName($query, $search, $id)
    {
        if (trim($search) != "") {
            $query->where("name", "LIKE", "$search%");

            if ($id) $query->where('id', '!=', $id);
        }
    }
    public function scopeEmail($query, $search, $id)
    {
        if (trim($search) != "") {
            $query->where("email", "LIKE", "$search%");

            if ($id) $query->where('id', '!=', $id);
        }
    }
    public function scopeType($query, $search)
    {
        if (trim($search) != "") {
            $query->where("type", $search);
        }
    }
    public function scopeBusiness($query, $search)
    {
        if (trim($search) != "") {
            $query->where("business", $search);
        }
    }
    public function scopeWithRelation($query)
    {
        $query->with('user', 'user_updated', 'buys.pedido', 'orders.nota', 'supplier.nota', 'exams', 'brands', 'metas');
    }
    public function scopePublish($query)
    {
        $query->whereNull('deleted_at');
    }
    // Other functions
    public function saveMetas($request)
    {
        $metadata = $this->metas()->where("key", "metadata")->first();
        $data = [
            "birthday" => "",
            "gender" => "",
        ];

        if (isset($request->birthday)) {
            $birthday = new Carbon($request->birthday, "America/Mexico_City");
            $data["birthday"] = $birthday->toDateString();
        } else if ($metadata) {
            $data["birthday"] = $metadata->value["birthday"];
        } else if ($this->birthday) {
            $birthday = new Carbon($this->birthday, "America/Mexico_City");
            $data["birthday"] = $birthday->toDateString();
        }

        if (isset($request->gender) && !empty($request->gender)) {
            $data["gender"] = $request->gender;
        } else if ($metadata) {
            if (array_key_exists("gender", $metadata->value)) {
                $data["gender"] = $metadata->value["gender"];
            }
        }

        if ($metadata) {
            $metadata->value = $data;
            $metadata->save();
        } else {
            $this->metas()->create(["key" => "metadata", "value" => $data]);
        }
    }
}
