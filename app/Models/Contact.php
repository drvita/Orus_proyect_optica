<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Exam;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\SearchService;
use App\DTOs\Search\SearchNameRequest;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ContactObserver;

#[ObservedBy([ContactObserver::class])]
class Contact extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "contacts";
    protected $fillable = [
        "name",
        "rfc",
        "email",
        "type",
        "telnumbers",
        "birthday",
        "domicilio",
        "user_id",
        "business",
        "updated_id"
    ];
    protected $hidden = ["updated_id", "user_id"];
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'birthday'
    ];
    protected $casts = [
        'telnumbers' => 'array',
        'domicilio' => 'array',
        'birthday' => 'date'
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
    public function buys()
    {
        return $this->hasMany(Sale::class, 'contact_id', 'id')->orderBy('created_at', 'DESC');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'contact_id', 'id')->orderBy('created_at', 'DESC');
    }
    public function supplier()
    {
        return $this->hasMany(Order::class, 'lab_id', 'id')->orderBy('created_at', 'DESC');
    }
    public function exams()
    {
        return $this->hasMany(Exam::class, 'contact_id', 'id')->orderBy('updated_at', 'DESC');
    }
    public function brands()
    {
        return $this->hasMany(Brand::class, 'contact_id', 'id')->orderBy('updated_at', 'DESC');
    }
    public function metas()
    {
        return $this->morphMany(Meta::class, 'metable');
    }
    public function phones()
    {
        return $this->morphMany(PhoneNumber::class, 'model');
    }

    // Attributes
    public function getAgeAttribute()
    {
        $birthday = $this->birthday;
        $meta_data = $this->metas()->where("key", "metadata")->first();
        if (!$birthday && $meta_data) {
            $birthday = new Carbon($meta_data->value["birthday"]);
        }
        if (is_string($birthday)) {
            $birthday = Carbon::parse($birthday);
        }

        $edad = $birthday !== null ? (int)$birthday->diffInYears(Carbon::now()) : 0;
        return $edad > 0 && $edad < 120 ? $edad : 0;
    }

    public function getEnUsoAttribute()
    {
        return ($this->buys_count ?? 0) +
            ($this->brands_count ?? 0) +
            ($this->exams_count ?? 0) +
            ($this->supplier_count ?? 0) +
            ($this->orders_count ?? 0);
    }

    public function getGenderAttribute()
    {
        $metadata = $this->metas->where('key', 'metadata')->first();
        return $metadata->value['gender'] ?? 'N/A';
    }

    //Scopes
    public function scopeSearchUser($query, $search)
    {
        if (trim($search) != "") {
            $query->Where('email', "LIKE", "$search%")
                ->orWhere('rfc', "LIKE", "$search%")
                ->orWhere(function ($q) use ($search) {
                    $this->scopeName($q, $search, null);
                });
        }
    }
    public function scopeName($query, $search, $exept)
    {
        if (trim($search) != "") {
            $query->where('name', 'LIKE', "%$search%");
            if ($exept) {
                Log::info("[Contact model] Scope name request exept id {$exept}");
                $query->where('id', '!=', $exept);
            }
        }
    }
    public function scopeEmail($query, $search, $exept)
    {
        if (trim($search) != "") {
            $query->where("email", "LIKE", "$search%");

            if ($exept) $query->where('id', '!=', $exept);
        }
    }
    public function scopeType($query, $search)
    {
        if (trim($search) != "") {
            $query->where("type", $search);
        }
    }
    public function scopeBusiness($query, $search)
    {
        if (trim($search) != "") {
            $query->where("business", $search);
        }
    }
    public function scopeWithUsageCounts($query)
    {
        $query->withCount(['buys', 'brands', 'exams', 'supplier', 'orders', 'phones']);
    }
    public function scopeWithRelation($query)
    {
        $query->with(
            'user',
            'user_updated',
            'buys.pedido',
            'orders.nota',
            'supplier.nota',
            'exams',
            'brands',
            'metas',
            'phones'
        );
    }
    public function scopeWithRelationShort($query)
    {
        $query->with(
            'user',
            'user_updated',
            'phones'
        );
    }

    /**
     * @deprecated This scope is deprecated and will be removed in future versions.
     * Use SoftDeletes global scope instead.
     */
    public function scopePublish($query)
    {
        $query->whereNull('deleted_at');
    }

    // Other functions
    private function searchService(string $name)
    {
        $request = new SearchNameRequest(
            name: $name,
            min_similarity: 0.5,
            limit: 10
        );
        $services =  new SearchService();
        return $services->searchName($request);
    }
    public function saveMetas(ContactRequest $request)
    {
        $metadata = $this->metas()->where("key", "metadata")->first();
        $data = [
            "birthday" => "",
            "gender" => "",
        ];

        if (isset($request->birthday)) {
            $birthday = new Carbon($request->birthday, "America/Mexico_City");
            $data["birthday"] = $birthday->toDateString();
        } else if ($metadata) {
            $data["birthday"] = $metadata->value["birthday"];
        } else if ($this->birthday) {
            $birthday = new Carbon($this->birthday, "America/Mexico_City");
            $data["birthday"] = $birthday->toDateString();
        }

        if (isset($request->gender) && !empty($request->gender)) {
            $data["gender"] = $request->gender;
        } else if ($metadata) {
            if (array_key_exists("gender", $metadata->value)) {
                $data["gender"] = $metadata->value["gender"];
            }
        }

        if ($metadata) {
            $metadata->value = $data;
            $metadata->save();
        } else {
            $this->metas()->create(["key" => "metadata", "value" => $data]);
        }
    }
}
