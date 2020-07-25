<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model{
    protected $table = "orders";
    protected $fillable = [
        "contact_id","exam_id","items","mensajes","ncaja","npedidolab","laboratorio","user_id",
        "observaciones","armazon_code","armazon_name"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function examen(){
        return $this->belongsTo('App\Models\Exam','exam_id');
    }
    public function paciente(){
        return $this->belongsTo('App\Models\Contact','contact_id');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
}
