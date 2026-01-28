<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamFunctions extends Model
{
    use HasFactory;

    protected $table = 'exam_functions';

    protected $fillable = [
        'exam_id',
        'fll',
        'fvl',
        'vvl',
        'bnl',
        'btl',
        'ccf',
        'arn',
        'arp',
        'add',
        'flc',
        'flc_100',
        'aca_a',
        'fvc',
        'vvc',
        'bnc',
        'btc',
        'fa_ao',
        'fa_od',
        'fa_oi',
        'ppcn',
        'ppca',
        'aa_neg_od',
        'aa_neg_oi',
    ];

    /**
     * Relationship with Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
