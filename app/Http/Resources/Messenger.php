<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Messenger extends JsonResource
{

    public function toArray($request)
    {
        $version = $request->query('version', 1);

        if ($version == 2) {
            return [
                'id' => $this->id,
                'message' => $this->message,
                'type' => $this->type,
                'media' => $this->media,
                'user_id' => $this->user_id,
                'messagable_id' => $this->messagable_id,
                'messagable_type' => $this->messagable_type,
                'creator' => new UserSimple($this->creador),
                'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : null,
                'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null,
            ];
        }

        $tableMap = [
            'App\Models\Order' => 'orders',
            'App\Models\Contact' => 'contacts',
            'App\Models\Exam' => 'exams',
        ];

        return [
            'id' => $this->id,
            'tabla' => $tableMap[$this->messagable_type] ?? $this->messagable_type,
            'registro' => $this->messagable_id,
            'mensaje' => $this->message,
            'para' => $this->user ? $this->user : null,
            'created_user' => $this->creador,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null,
        ];
    }
}
