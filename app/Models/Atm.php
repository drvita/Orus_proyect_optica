<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atm extends Model{
    protected $table = "atms";
    protected $fillable = [
        "efectivo","session","type","motivo","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function scopeDate($query, $search){
        if(trim($search) != ""){
            $query->WhereDate("created_at",$search);
        }
    }
    public function scopeUser($query, $user, $rol){
        if(trim($user) != ""){
            $user = $user * 1;
            if(!$rol->rol && $user > 1){
                $query->where('user_id',$user);
            } else if($rol->rol) {
                $query->where('user_id',$rol->id);
            }
        } else {
            if($rol->rol){
                $query->where('user_id',$rol->id);
            }
        }
    }
}
