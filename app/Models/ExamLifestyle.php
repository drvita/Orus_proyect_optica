<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamLifestyle extends Model
{
    use HasFactory;

    protected $table = 'exam_lifestyle';

    protected $fillable = [
        'exam_id',
        'pc',
        'pc_time',
        'lap',
        'lap_time',
        'tablet',
        'tablet_time',
        'movil',
        'movil_time',
        'cefalea',
        'c_frecuencia',
        'c_intensidad',
        'frontal',
        'temporal',
        'occipital',
        'temporaoi',
        'temporaod',
        'interrogatorio',
        'coa',
        'aopp',
        'aopf',
        'd_media',
        'd_test',
        'd_fclod',
        'd_fclod_time',
        'd_fcloi',
        'd_fcloi_time',
        'd_time',
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
