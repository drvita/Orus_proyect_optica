<?php

namespace App\DTOs\Search;

class CreateNameRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $id,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
        ];
    }
}
