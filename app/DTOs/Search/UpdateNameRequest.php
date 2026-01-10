<?php

namespace App\DTOs\Search;

class UpdateNameRequest
{
    public function __construct(
        public readonly string $point_id,
        public readonly string $name,
        public readonly string $id,
    ) {}

    public function toArray(): array
    {
        return [
            'point_id' => $this->point_id,
            'name' => $this->name,
            'id' => $this->id,
        ];
    }
}

