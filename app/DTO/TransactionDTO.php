<?php

namespace App\Dto;

use App\Currency;
use App\TransactionType;
use App\ValuesObject\Money;

final readonly class TransactionDTO
{
    private function __construct(
        public Money $amount,
        public TransactionType $type,
        public int $categoryId,
        public ?string $description = null
    ) {}

    public static function from(array $data): self
    {
        return new self(
            amount: Money::fromDecimalString($data['amount'], Currency::RUB),
            type: TransactionType::from($data['type']),
            categoryId: $data['category_id'],
            description: $data['description'] ?? null,
        );
    }
}
