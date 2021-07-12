<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Messenger extends Model{
    protected $table = "messengers";
    protected $fillable = [
        "table","idRow","user","message","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function creador(){
        return $this->belongsTo(User::class, 'user_id');
    }
    //scopes
    public function scopeTable($query, $search){
        if(trim($search) != ""){
            $query->where("table",$search);
        }
    }
    public function scopeIdRow($query, $search){
        if(trim($search) != ""){
            $query->where("idRow",$search);
        }
    }
}
