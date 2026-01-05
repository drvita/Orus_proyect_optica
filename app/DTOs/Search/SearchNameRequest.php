<?php

namespace App\DTOs\Search;

class SearchNameRequest
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $limit = 5,
        public readonly ?float $min_similarity = 0.6,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'limit' => $this->limit,
            'min_similarity' => $this->min_similarity,
        ]);
    }
}
