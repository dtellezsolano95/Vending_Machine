<?php

namespace App\Domain\Money;

use App\Domain\Money\Exception\InvalidCoinValueException;

class Coin
{
    private const VALID_COINS = [0.05, 0.10, 0.25, 1.00];

    private float $value;

    public function __construct(float $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(float $value): void
    {
        if (!in_array($value, self::VALID_COINS, true)) {
            throw InvalidCoinValueException::forValue($value, self::VALID_COINS);
        }
    }

    public function value(): float
    {
        return $this->value;
    }

    public static function validCoins(): array
    {
        return self::VALID_COINS;
    }
}
