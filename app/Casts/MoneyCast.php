<?php

namespace App\Casts;

use App\Currency;
use App\ValuesObject\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class MoneyCast implements CastsAttributes
{
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): Money {
        return Money::fromDecimalString((string) $value, Currency::RUB);
    }

    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): string {
        if (! $value instanceof Money) {
            throw new InvalidArgumentException('Value must be an instance of Money');
        }

        return $value->toDecimalString();
    }
}
