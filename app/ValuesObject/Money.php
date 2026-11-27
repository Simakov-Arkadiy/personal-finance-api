<?php

namespace App\ValuesObject;

use App\Currency;

final readonly class Money
{
    private function __construct(
        private int $amount,
        private Currency $currency,
    ) {}

    public static function createMoney(int $amount, Currency $currency): self
    {
        return new self($amount, $currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurency(): Currency
    {
        return $this->currency;
    }

    public static function fromDecimalString(string $amount, Currency $currency): self
    {
        $scale = $currency->getMinorMoneyUnitScale();

        if ($scale === 0) {
            return self::createMoney((int) $amount, $currency);
        }

        $parts = explode('.', $amount);

        $whole = (int) $parts[0];
        $fraction = (int) str_pad($parts[1] ?? '', $scale, '0');

        $minorAmount = $whole * (10 ** $scale) + $fraction;

        return self::createMoney($minorAmount, $currency);
    }

    public function toDecimalString(): string
    {
        $scale = $this->currency->getMinorMoneyUnitScale();

        if ($scale === 0) {
            return (string) $this->amount;
        }

        $divisor = 10 ** $scale;

        $whole = intdiv($this->amount, $divisor);
        $fraction = abs($this->amount % $divisor);

        return sprintf(
            '%d.%0'.$scale.'d',
            $whole,
            $fraction,
        );
    }
}
