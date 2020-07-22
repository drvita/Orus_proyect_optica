<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Contact extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){

        Carbon::setLocale('es');
        $create_at = new Carbon($this->created_at);
        $updated_at = new Carbon($this->updated_at);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'rfc' => $this->rfc,
            'email' => $this->email,
            'type' => $this->type,
            'telnumbers' => $this->telnumbers,
            'birthday' => $this->birthday,
            'domicilio' => $this->domicilio,
            'user_create' => $this->user->name,
            'created_at' => $create_at->diffForHumans(),
            'updated_at' => $updated_at->diffForHumans(),
        ];
    }
}
