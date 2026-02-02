<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
use App\Models\Exam;
use App\Models\Contact;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = "orders";
    protected $auditActivities = ["updated", "deleted", "created", "restored"];
    protected $fillable = [
        "contact_id",
        "exam_id",
        "ncaja",
        "npedidolab",
        "lab_id",
        "user_id",
        "observaciones",
        "session",
        "status",
        "branch_id",
        "updated_id"
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

    //Scopes
    public function scopePatient($query, null | int $id)
    {
        if (!is_null($id)) {
            $query->where("contact_id", $id);
        }
    }
    public function scopeSearch($query, string | null $search)
    {
        if (!is_null($search) && !empty($search)) {
            $search = trim($search);
            $query->whereHas('paciente', function ($query) use ($search) {
                $query->where('name', "LIKE", "%$search%");
            })
                ->orWhere("id", $search);
        }
    }
    public function scopeStatus($query, null | int $state)
    {
        if (!is_null($state)) {
            $query->where("status", $state);
        }
    }
    public function scopeWithRelation($query)
    {
        $query->with('examen', 'paciente.phones', 'laboratorio', 'user', 'nota', 'items');
    }
    public function scopeBranch($query, null | int $branch_id)
    {
        if (!is_null($branch_id)) {
            $query->where("branch_id", $branch_id);
        }
    }
}
