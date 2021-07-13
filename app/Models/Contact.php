<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Exam;
use App\Models\Brand;

class Contact extends Model{
    protected $table = "contacts";
    //type 0 is customer contact  //business is a company : 1
    //Type 1 is supply contact    //Business not is company: 0,
    protected $fillable = [
        "name","rfc","email","type","telnumbers","birthday","domicilio","user_id","business","updated_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'birthday'
    ];
    //Relations 
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_id', 'id');
    }
    public function buys(){
        return $this->hasMany(Sale::class,'contact_id', 'id')->orderBy('created_at','DESC');
    }
    public function orders(){
        return $this->hasMany(Order::class,'contact_id', 'id')->orderBy('created_at','DESC');
    }
    public function supplier(){
        return $this->hasMany(Order::class,'lab_id', 'id')->orderBy('created_at','DESC');
    }
    public function exams(){
        return $this->hasMany(Exam::class,'contact_id', 'id')->orderBy('updated_at','DESC');
    }
    public function brands(){
        return $this->hasMany(Brand::class,'contact_id', 'id')->orderBy('updated_at','DESC');
    }
    //Scopes
    public function scopeSearchUser($query, $search, $id){
        if(trim($search) != ""){
            $query->where(function($q) use($search, $id){
                $q->where('name',"LIKE","%$search%")
                    ->orWhere('email',"LIKE","$search%")
                    ->orWhere('rfc',"LIKE","$search%")
                    ->orWhere('id',$search);
            });

            if($id) $query->where('id', '!=', $id);
            //dd($query->toSql());
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
    public function scopeWithRelation($query){
        $query->with('user','user_updated','buys.pedido','orders.nota','supplier.nota','exams','brands');
    }
    public function scopePublish($query){
        $query->whereNull('deleted_at');
    }
    /*
    public function scopeHasOrder($query, $search = false, $state = null){
        if($search){
            $query->select('contacts.*')
                    ->leftJoin('orders', 'contacts.id', '=', 'orders.contact_id');
            if($state) $query->where('orders.id', "!=", null);
            else $query->where('orders.id', null);
        }
    }
    */
}
