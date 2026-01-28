<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamLifestyleSimple extends JsonResource
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
            'pc' => $this->pc ? true : false,
            'pc_time' => $this->pc_time,
            'lap' => $this->lap ? true : false,
            'lap_time' => $this->lap_time,
            'tablet' => $this->tablet ? true : false,
            'tablet_time' => $this->tablet_time,
            'movil' => $this->movil ? true : false,
            'movil_time' => $this->movil_time,
            'cefalea' => $this->cefalea ? true : false,
            'c_frecuencia' => $this->c_frecuencia,
            'c_intensidad' => $this->c_intensidad,
            'frontal' => $this->frontal ? true : false,
            'temporal' => $this->temporal ? true : false,
            'occipital' => $this->occipital ? true : false,
            'temporaoi' => $this->temporaoi ? true : false,
            'temporaod' => $this->temporaod ? true : false,
            'interrogatorio' => $this->interrogatorio,
            'coa' => $this->coa,
            'aopp' => $this->aopp,
            'aopf' => $this->aopf,
            'd_media' => $this->d_media,
            'd_test' => $this->d_test,
            'd_fclod' => $this->d_fclod ? true : false,
            'd_fclod_time' => $this->d_fclod_time,
            'd_fcloi' => $this->d_fcloi ? true : false,
            'd_fcloi_time' => $this->d_fcloi_time,
            'd_time' => $this->d_time,
        ];
    }
}
