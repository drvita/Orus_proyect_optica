<?php

namespace App\Http\Resources;
use App\Http\Resources\ContactShort;
use App\Http\Resources\ExamShort;
use App\Http\Resources\SaleItemShort;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource{

    public function toArray($request){
        $items = is_string($this->items) ? json_decode($this->items, true) : $this->items;

        
        $return['id'] = $this->id;
        $return['paciente'] = new ContactShort($this->paciente);
        $return['exam'] = new ExamShort($this->examen);
        $return['session'] = $this->session;
        $return['items'] = SaleItemShort::collection($items);
        $return['ncaja'] = $this->ncaja;
        $return['npedidolab'] = $this->npedidolab;
        $return['laboratorio'] = new ContactShort($this->laboratorio);
        $return['observaciones'] = $this->observaciones;
        $return['status'] = $this->status;
        $return['nota'] = new SaleInOrder($this->nota);
        $return['created'] = new UserInExam($this->user);
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');

        //dd($return);
        return $return;
    }
}
