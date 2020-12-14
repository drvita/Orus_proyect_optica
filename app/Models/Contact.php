<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model{
    protected $table = "contacts";
    protected $fillable = [
        "name","rfc","email","type","telnumbers","birthday","domicilio","user_id","business"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at',
        'birthday'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function buys(){
        return $this->hasMany('App\Models\Sale','contact_id', 'id');
    }
    public function orders(){
        return $this->hasMany('App\Models\Order','contact_id', 'id');
    }
    public function supplier(){
        return $this->hasMany('App\Models\Order','lab_id', 'id');
    }
    public function exams(){
        return $this->hasMany('App\Models\Exam','contact_id', 'id');
    }
    public function brands(){
        return $this->hasMany('App\Models\Brand','contact_id', 'id');
    }
    public function scopeSearchUser($query, $search){
        if(trim($search) != ""){
            $query->where(function($q) use($search){
                $q->where('name',"LIKE","%$search%")
                    ->orWhere('email',"LIKE","$search%")
                    ->orWhere('rfc',"LIKE","$search%");
            });
        }
    }
    public function scopeName($query, $search){
        if(trim($search) != ""){
            $query->where("name","LIKE","$search%");
        }
    }
    public function scopeEmail($query, $search){
        if(trim($search) != ""){
            $query->where("email","LIKE","$search%");
        }
    }
    public function scopeType($query, $search){
        if(trim($search) != ""){
            $query->where("type",$search);
        }
    }
    public function scopeBusiness($query, $search){
        if(trim($search) != ""){
            $query->where("business",$search);
        }
    }
}
