<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Messenger;
use App\Models\Config;
use App\User;
use Illuminate\Support\Facades\Auth;

class Payment extends Model{
    protected $table = "payments";
    protected $fillable = [
        "metodopago","details","bank_id","auth","total","sale_id","contact_id","user_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function bankName(){
        return $this->belongsTo(Config::class, 'bank_id', 'id');
    }
    public function SaleDetails(){
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
    public function scopeSale($query, $search){
        if(trim($search) != ""){
            $query->where("sale_id",$search);
        }
    }
    public function scopeUser($query, $rol, $user){
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
    public function scopeMethodPay($query, $date, $rol, $user){
        if($date != "" && trim($rol) != ""){
            $query->select('metodopago')
                ->selectRaw('SUM(total) as total')
                ->WhereDate("created_at",$date)
                ->groupBy('metodopago');
           
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
    public function scopeBankDetails($query, $date, $rol, $user){
        if($date != "" && trim($rol) != ""){
            $query->select('bank_id')
                ->selectRaw('SUM(total) as total')
                ->WhereDate("created_at",$date)
                ->Where("bank_id", "!=", 0)
                ->groupBy('bank_id');
           
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
    public function scopeDate($query, $search){
        if(trim($search) != ""){
            $query->WhereDate("created_at",$search);
        }
    }

    protected static function booted(){
        static::created(function ($pay) {
            $updateSale = Sale::find($pay->sale_id);

            if($updateSale->order_id){
                $messegeId = $updateSale->order_id;
                $table = "orders";
            } else {
                $messegeId = $pay->sale_id;
                $table = "sales";
            }

            $updateSale->pagado += $pay->total;
            $updateSale->save();
            Messenger::create([
                "table" => $table,
                "idRow" => $messegeId,
                "message" => Auth::user()->name ." abono a la cuenta ($ ". $pay->total .")",
                "user_id" => 1
            ]);
        });
        static::deleted(function ($pay) {
            $updateSale = Sale::find($pay->sale_id);

            if($updateSale->order_id){
                $messegeId = $updateSale->order_id;
                $table = "orders";
            } else {
                $messegeId = $pay->sale_id;
                $table = "sales";
            }

            $updateSale->pagado -= $pay->total;
            $updateSale->save();
            Messenger::create([
                "table" => $table,
                "idRow" => $messegeId,
                "message" => Auth::user()->name ." elimino un abono ($ ". $pay->total .")",
                "user_id" => 1
            ]);
        });
    }
}
