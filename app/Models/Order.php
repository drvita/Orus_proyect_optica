<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
use App\Models\Exam;
use App\Models\Contact;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Illuminate\Support\Facades\Log;

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
        "updated_id",
        "delivered_at"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'delivered_at'
    ];
    const STATUS_PENDING = 0;
    const STATUS_LABORATORY = 1;
    const STATUS_BOX = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_DELIVERED = 4;
    const STATUS_CANCELLED = 5;

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
    public function sale()
    {
        return $this->hasOne(Sale::class, 'order_id', 'id');
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
        $query->with([
            'examen',
            'paciente.phones',
            'paciente.user',
            'paciente.user_updated',
            'laboratorio',
            'user',
            'nota',
            'items'
        ]);
    }
    public function scopeBranch($query, null | int $branch_id)
    {
        if (!is_null($branch_id)) {
            $query->where("branch_id", $branch_id);
        }
    }

    // Functions
    public function createSale(array | object | null $data = null)
    {
        try {
            return $this->sale()->create([
                "session" => $this->session,
                "subtotal" => $data['subtotal'] ?? 0,
                "descuento" => $data['descuento'] ?? 0,
                "total" => $data['total'] ?? 0,
                "pagado" => $data['pagado'] ?? 0,
                "contact_id" => $this->contact_id,
                "order_id" => $this->id,
                "user_id" => $this->user_id,
                "branch_id" => $this->branch_id,
            ]);
        } catch (\Throwable $th) {
            Log::error("[Order.createSale] Error creating sale: " . $th->getMessage());
            return null;
        }
    }

    // Accessors
    public function getIsLabCompleteAttribute()
    {
        $allItemsAssigned = false;
        $items = $this->items;

        if ($items && $items->count() > 0) {
            $allItemsAssigned = $items->every(function ($item) {
                return ($item->out ?? 0) > 0 || ($item->inStorage ?? 0) > 0;
            });
        }

        return (!empty($this->npedidolab) && !empty($this->lab_id)) || $allItemsAssigned;
    }
}
