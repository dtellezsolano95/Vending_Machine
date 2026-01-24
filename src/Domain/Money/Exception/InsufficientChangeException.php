<?php

namespace App\Domain\Money\Exception;

class InsufficientChangeException extends \Exception
{
    public static function forAmount(float $amount): self
    {
        return new self(sprintf('NO change available to return %.2f', $amount));
    }
}
