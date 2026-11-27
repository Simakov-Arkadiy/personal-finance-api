<?php

namespace App;

enum Currency: string
{
    case RUB = 'RUB';
    case USD = 'USD';
    case EUR = 'EUR';
    case JPY = 'JPY';

    public function getMinorMoneyUnitScale(): int
    {
        return match ($this) {
            self::RUB,self::USD,self::EUR => 2,
            self::JPY => 0,
        };
    }
}
