<?php

namespace App\Domain\Product\Exception;

class InsufficientStockException extends \Exception
{
    public static function forProduct(string $productName): self
    {
        return new self(sprintf('Insufficient stock for product "%s"', $productName));
    }
}
