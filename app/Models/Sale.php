<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Contact;
use App\Models\Order;
use App\Models\SaleItem;
use App\Models\Payment;

class Sale extends Model
{
    protected $table = "sales";
    protected $fillable = [
        "subtotal", "descuento", "total", "pagado", "contact_id", "order_id", "user_id", "session", "branch_id"
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
    public function cliente()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function pedido()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class, 'session', 'session');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'sale_id', 'id')->whereNull('payments.deleted_at');
    }
    public function branch()
    {
        return $this->belongsTo(Config::class, 'branch_id', 'id');
    }
    //Scopes
    public function scopeRelations($query)
    {
        $query->with('user', 'cliente', 'pedido', 'items', 'payments');
    }
    public function scopeCliente($query, $name)
    {
        $name = preg_replace('/\d+/', "", $name);
        if (trim($name) != "" && is_string($name)) {
            $query->whereHas('cliente', function ($query) use ($name) {
                $query->where('name', "LIKE", "%$name%");
            });
        }
    }
    public function scopeSearchId($query, $search)
    {
        if (trim($search) != "") {
            $search = (int) $search;
            if (is_numeric($search) && $search > 0) {
                $query->Where("id", $search);
            }
        }
    }
    public function scopeType($query, $search)
    {
        if (trim($search) != "") {
            settype($search, "boolean");
            if ($search)
                $query->whereColumn("pagado", "total");
            else
                $query->whereColumn("pagado", "<", "total");
        }
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
}