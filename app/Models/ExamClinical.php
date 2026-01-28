<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamClinical extends Model
{
    use HasFactory;

    protected $table = 'exam_clinical';

    protected $fillable = [
        'exam_id',
        'avf2o',
        'avslod',
        'avcgaod',
        'avfod',
        'piod',
        'keratometriaod',
        'rsod',
        'avsloi',
        'avcgaoi',
        'avfoi',
        'pioi',
        'keratometriaoi',
        'rsoi',
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
