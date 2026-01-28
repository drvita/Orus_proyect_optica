<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamFunctionsSimple extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'fll' => $this->fll,
            'fvl' => $this->fvl,
            'vvl' => $this->vvl,
            'bnl' => $this->bnl,
            'btl' => $this->btl,
            'ccf' => $this->ccf,
            'arn' => $this->arn,
            'arp' => $this->arp,
            'add' => $this->add,
            'flc' => $this->flc,
            'flc_100' => $this->flc_100,
            'aca_a' => $this->aca_a,
            'fvc' => $this->fvc,
            'vvc' => $this->vvc,
            'bnc' => $this->bnc,
            'btc' => $this->btc,
            'fa_ao' => $this->fa_ao,
            'fa_od' => $this->fa_od,
            'fa_oi' => $this->fa_oi,
            'ppcn' => $this->ppcn,
            'ppca' => $this->ppca,
            'aa_neg_od' => $this->aa_neg_od,
            'aa_neg_oi' => $this->aa_neg_oi,
        ];
    }
}
