<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Messenger;
use App\Models\Config;
use App\User;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    protected $table = "payments";
    protected $fillable = [
        "metodopago", "details", "bank_id", "auth", "total", "sale_id", "contact_id", "user_id", "branch_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
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
    public function bankName()
    {
        return $this->belongsTo(Config::class, 'bank_id', 'id');
    }
    public function SaleDetails()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Config::class, 'branch_id', 'id');
    }
    //Scopes
    public function scopeSale($query, $search)
    {
        if (trim($search) != "") {
            $query->where("sale_id", $search);
        }
    }
    public function scopeUser($query, $rol, $user)
    {
        if (trim($user) != "") {
            $user = intval($user);
            if ($user) $query->where('user_id', $user);
        }
    }
    public function scopeDateStart($query, $search)
    {
        if (trim($search) != "") {
            $query->WhereDate("created_at", ">=", $search);
        }
    }
    public function scopeDateFinish($query, $search)
    {
        if (trim($search) != "") {
            $query->WhereDate("created_at", "<=", $search);
        }
    }
    public function scopeMethodPay($query)
    {
        $query->select('metodopago')
            ->selectRaw('SUM(total) as total')
            ->groupBy('metodopago');
    }
    public function scopeBranchId($query, $search)
    {
        if (trim($search) != "") {
            $query->where("branch_id", (int) $search);
        }
    }
    public function scopeProtected($query, $currentUser, $user)
    {
        $userRole = $currentUser->getRoleNames()[0];

        if (trim($user) != "") {
            $user = (int) $user;

            if ($userRole === "admin" && $user > 1) {
                $query->where('user_id', $user);
            } else {
                $query->where('user_id', $currentUser->id);
            }
        } else {
            if ($userRole !== "admin") {
                $query->where('user_id', $currentUser->id);
            }
        }
    }
    public function scopeBankDetails($query)
    {
        $query->select('bank_id')
            ->selectRaw('SUM(total) as total')
            ->Where("bank_id", "!=", 0)
            ->groupBy('bank_id');
    }
    public function scopeDate($query, $search)
    {
        if (trim($search) != "") {
            $query->WhereDate("created_at", $search);
        }
    }
    public function scopePublish($query)
    {
        $query->whereNull('deleted_at');
    }
    public function scopeBranch($query, $search)
    {
        if (trim($search) != "") {
            $query->where("branch_id", $search);
        }
    }
    //Statics functions
    protected static function booted()
    {
        static::created(function ($pay) {
            $updateSale = Sale::find($pay->sale_id);

            if ($updateSale->order_id) {
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
                "message" => Auth::user()->name . " abono a la cuenta ($ " . $pay->total . ")",
                "user_id" => 1
            ]);
        });
        static::deleted(function ($pay) {
            $updateSale = Sale::find($pay->sale_id);

            if ($updateSale->order_id) {
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
                "message" => Auth::user()->name . " elimino un abono ($ " . $pay->total . ")",
                "user_id" => 1
            ]);
        });
    }
}