<?php

namespace App\Dto;

use App\TransactionType;

readonly class TransactionDTO
{
    public function __construct(
        public string $amount,
        public TransactionType $type,
        public int $categoryId,
        public ?string $description = null
    ) {}

    public static function from(array $data): self
    {
        return new self(
            amount: $data['amount'],
            type: TransactionType::from($data['type']),
            categoryId: $data['category_id'],
            description: $data['description'] ?? null,
        );
    }
}
