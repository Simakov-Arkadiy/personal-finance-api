<?php

namespace App\Dto;

use App\TransactionType;

final readonly class CategoryDTO
{
    private function __construct(
        public string $name,
        public TransactionType $type,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            name: $data['name'],
            type: TransactionType::from($data['type']),
        );
    }
}
