<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Contact;

class Brand extends Model{
    protected $table = "brands";
    protected $fillable = [
        "name","contact_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function proveedor(){
        return $this->belongsTo(Contact::class,'contact_id');
    }
    public function scopeSupplier($query, $search){
        if(trim($search) != ""){
            $query->where("contact_id",$search);
        }
    }
}
