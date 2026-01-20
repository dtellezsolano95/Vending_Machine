<?php

namespace App\Domain\Money\Exception;

use InvalidArgumentException;

class InvalidCoinValueException extends InvalidArgumentException
{
    public static function forValue(float $value, array $validCoins): self
    {
        return new self(
            sprintf(
                'Invalid coin value: %s. Valid coins are: %s',
                $value,
                implode(', ', $validCoins)
            )
        );
    }
}
