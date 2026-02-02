<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ExamObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

#[ObservedBy([ExamObserver::class])]
class Exam extends Model
{
    use SoftDeletes, Auditable;

    protected $table = "exams";
    protected $fillable = [
        "edad",
        "keratometriaoi",
        "keratometriaod",
        "pantalleooi",
        "pantalleood",
        "interrogatorio",
        "cefalea",
        "c_frecuencia",
        "c_intensidad",
        "frontal",
        "temporal",
        "occipital",
        "generality",
        "temporaoi",
        "temporaod",
        "coa",
        "aopp",
        "aopf",
        "avsloi",
        "avslod",
        "avcgaoi",
        "avcgaod",
        "cvoi",
        "cvod",
        "oftalmoscopia",
        "rsoi",
        "rsod",
        "diagnostico",
        "presbicie",
        "txoftalmico",
        "esferaoi",
        "esferaod",
        "cilindroi",
        "cilindrod",
        "ejeoi",
        "ejeod",
        "adicioni",
        "adiciond",
        "dpoi",
        "dpod",
        "avfod",
        "avfoi",
        "avf2o",
        "lcmarca",
        "lcgoi",
        "lcgod",
        "txoptico",
        "alturaod",
        "alturaoi",
        "pioi",
        "piod",
        "observaciones",
        "pc",
        "tablet",
        "movil",
        "lap",
        "lap_time",
        "pc_time",
        "tablet_time",
        "movil_time",
        "d_time",
        "d_media",
        "d_test",
        "d_fclod",
        "d_fcloi",
        "d_fclod_time",
        "d_fcloi_time",
        "status",
        "contact_id",
        "user_id",
        "category_id",
        "category_ii",
        "adicion_media_oi",
        "adicion_media_od",
        "branch_id",
        "updated_id",
        "started_at",
        "ended_at"
    ];
    protected $hidden = ['category_id', 'category_ii', 'user_id', 'contact_id', 'updated_id'];
    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    //Relationships
    public function paciente()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_id', 'id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'exam_id', 'id');
    }
    public function categoryPrimary()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    public function categorySecondary()
    {
        return $this->hasOne(Category::class, 'id', 'category_ii');
    }
    public function branch()
    {
        return $this->belongsTo(Config::class, 'branch_id', 'id');
    }
    public function lifestyle()
    {
        return $this->hasOne(ExamLifestyle::class, 'exam_id');
    }
    public function clinical()
    {
        return $this->hasOne(ExamClinical::class, 'exam_id');
    }
    public function functions()
    {
        return $this->hasOne(ExamFunctions::class, 'exam_id');
    }

    // Attributes
    public function getDurationAttribute()
    {
        $start = $this->started_at;
        $end = $this->ended_at;

        if (!$start || !$end) {
            return null;
        }

        try {
            $start = $start instanceof Carbon ? $start : Carbon::parse($start);
            $end = $end instanceof Carbon ? $end : Carbon::parse($end);

            return round($start->floatDiffInMinutes($end), 2);
        } catch (\Exception $e) {
            Log::error("[Exam] Error calculating duration: " . $e->getMessage());
            return null;
        }
    }
    public function getBeforeGraduationMetaAttribute()
    {
        return $this->metas()
            ->where("key", "before_graduation")
            ->orderBy("id", "desc")
            ->first();
    }

    //Scopes
    public function scopeSearch($query, string | null $search)
    {
        if (!is_null($search) && !empty($search)) {
            $query->whereHas('paciente', function ($query) use ($search) {
                $query->where('name', "LIKE", "%$search%");
            })
                ->orWhere('id', $search);
        }
    }
    public function scopeByPatient($query, int | null $patient_id)
    {
        if (!is_null($patient_id)) {
            $query->where("contact_id", $patient_id);
        }
    }
    public function scopeDate($query, $search)
    {
        if (trim($search) != "") {
            $query->WhereDate("created_at", $search);
        }
    }
    public function scopeStatus($query, $search)
    {
        if (trim($search) != "") {
            $query->where("status", $search);
        }
    }
    public function scopeWithRelation($query, int $version = 1)
    {
        if ($version == 1) {
            $query->with([
                'paciente.metas',
                'user',
                'orders.nota',
                'orders.branch',
                'categoryPrimary',
                'categorySecondary',
                'branch',
                'metas'
            ]);
        } else {
            $query->with([
                'paciente.metas',
                'user',
                'orders.nota',
                'orders.branch',
                'categoryPrimary',
                'categorySecondary',
                'branch',
                'metas',
                'lifestyle',
                'clinical',
                'functions'
            ]);
        }
    }
    public function scopeBranch($query, null | int $branch_id)
    {
        if (!is_null($branch_id)) {
            $query->where("branch_id", $branch_id);
        }
    }

    // Functions
    public function startExam()
    {
        $this->update([
            "started_at" => now(),
        ]);
    }
    public function endExam()
    {
        $this->update([
            "ended_at" => now(),
        ]);
    }
}
