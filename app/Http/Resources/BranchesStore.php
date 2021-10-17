<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function GuzzleHttp\json_decode;

class BranchesStore extends JsonResource{

    public function toArray($request){
        $return = [];

        if(isset($this->id)){
            $data = json_decode($this->value, true);

            $return['id'] = $this->id;
            $return['name'] = $data->name;
        }
        
        return $return;
    }
}