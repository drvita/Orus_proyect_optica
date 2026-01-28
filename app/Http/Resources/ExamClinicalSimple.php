<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamClinicalSimple extends JsonResource
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
            'avf2o' => $this->avf2o,
            'avslod' => $this->avslod,
            'avcgaod' => $this->avcgaod,
            'avfod' => $this->avfod,
            'piod' => $this->piod,
            'keratometriaod' => $this->keratometriaod,
            'rsod' => $this->rsod,
            'avsloi' => $this->avsloi,
            'avcgaoi' => $this->avcgaoi,
            'avfoi' => $this->avfoi,
            'pioi' => $this->pioi,
            'keratometriaoi' => $this->keratometriaoi,
            'rsoi' => $this->rsoi,
        ];
    }
}
