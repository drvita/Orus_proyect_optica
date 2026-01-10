<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Messenger;
use App\Models\Config;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Payment extends Model
{
    protected $table = "payments";
    protected $fillable = [
        "metodopago",
        "details",
        "bank_id",
        "auth",
        "total",
        "sale_id",
        "contact_id",
        "user_id",
        "branch_id",
        "updated_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at'
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
    public function metas()
    {
        return $this->morphMany(Meta::class, 'metable');
    }
    //Scopes
    public function scopeSale($query, $search)
    {
        if (trim($search) != "") {
            $query->where("sale_id", $search);
        }
    }
    public function scopeUser($query, $search)
    {
        $user_id = trim($search);
        if (is_numeric($user_id)) $user_id = (int) $user_id;
        else $user_id = 0;

        if ((bool) $user_id) {
            $query->where('user_id', $user_id);
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
    //Other functions
    public function setMessage($table, $messegeId, $message)
    {
        Messenger::create([
            "table" => $table,
            "idRow" => $messegeId,
            "message" => $message,
            "user_id" => 1
        ]);
    }
    protected static function booted()
    {
        parent::boot();

        static::created(function (Payment $pay) {
            $sale = Sale::find($pay->sale_id);
            $auth = Auth::user();

            //delete
            if (!$auth) {
                $auth = User::where("id", 2)->first();
            }

            if ($sale->order_id) {
                $messegeId = $sale->order_id;
                $table = "orders";
            } else {
                $messegeId = $pay->sale_id;
                $table = "sales";
            }

            $dataPay = [
                "total" => $pay->total,
                "method" => $pay->metodopago
            ];
            $data = ["user_id" => $auth->id ? $auth->id : 1, "inputs" => $dataPay];
            $sale->metas()->create(["key" => "created payment", "value" => $data, "datetime" => Carbon::now()]);
            $pay->setMessage($table, $messegeId, $auth->name . " abono a la cuenta ($ " . $pay->total . ")");
        });
        static::deleted(function (Payment $pay) {
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
            $pay->setMessage($table, $messegeId, Auth::user()->name . " elimino un abono ($ " . $pay->total . ")");
        });
        static::updated(function (Payment $pay) {
            $type = "";
            $auth = Auth::user();
            //delete
            if (!$auth) {
                $auth = User::where("id", 2)->first();
            }
            $dataPay = [
                "total" => $pay->total,
                "method" => $pay->metodopago
            ];
            $data = ["user_id" => $auth->id, "inputs" => $dataPay];
            $sale = Sale::find($pay->sale_id);

            if (is_null($pay->deleted_at)) {
                $data['datetime'] = Carbon::now();
                $type = "updated payment";
            } else {
                $data['datetime'] = Carbon::now();
                $type = "deleted payment";

                if ($sale->order_id) {
                    $messegeId = $sale->order_id;
                    $table = "orders";
                } else {
                    $messegeId = $pay->sale_id;
                    $table = "sales";
                }

                $pay->setMessage($table, $messegeId, $auth->name . " elimino un abono ($ " . $pay->total . ")");
            }

            $sale->metas()->create(["key" => $type, "value" => $data]);
            // dd(["key" => $type, "value" => $data]);
        });
    }
}
