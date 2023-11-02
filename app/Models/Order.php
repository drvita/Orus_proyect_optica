<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
use App\Models\Exam;
use App\Models\Contact;
use App\User;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = [
        "contact_id", "exam_id", "ncaja", "npedidolab", "lab_id", "observaciones", "session", "status", "updated_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
    //Relationships
    public function examen()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
    public function paciente()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function laboratorio()
    {
        return $this->belongsTo(Contact::class, 'lab_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_id', 'id');
    }
    public function nota()
    {
        return $this->belongsTo(Sale::class, 'id', 'order_id');
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class, 'session', 'session');
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
    public function scopePaciente($query, $name)
    {
        $name = preg_replace('/\d+/', "", $name);
        if (trim($name) != "" && is_string($name)) {
            $query->whereHas('paciente', function ($query) use ($name) {
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
    public function scopeEstado($query, $search)
    {
        if (trim($search) != "") {
            $query->where("status", $search);
        }
    }
    public function scopeWithRelation($query)
    {
        $query->with('examen', 'paciente', 'laboratorio', 'user', 'nota', 'items');
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

    // Listerning 
    protected static function booted()
    {
        parent::boot();
        static::creating(function ($item) {
            $user = auth()->user();
            $item->user_id = $user->id;
            $item->branch_id = $user->branch_id;
            $item->status = 0;
        });

        static::updated(function ($item) {
            $type = "";
            $auth = Auth::user();
            $dirty = $item->getDirty();
            unset($dirty['updated_at']);
            unset($dirty['updated_id']);
            unset($dirty['user_id']);
            $data = ["user_id" => $auth->id, "inputs" => $dirty];

            if (!$dirty || !count($dirty)) return;

            if (is_null($item->deleted_at)) {
                $data['datetime'] = Carbon::now();
                $type = "updated";
            } else {
                $data['datetime'] = Carbon::now();
                $type = "deleted";
            }

            $item->metas()->create(["key" => $type, "value" => $data]);
        });
    }
}
