<?php

namespace App\Domain\Product;

final class JuiceProduct implements ProductInterface
{
    private const PRODUCT_NAME = 'JUICE';
    private const PRODUCT_PRICE = 1.00;

    public function name(): string
    {
        return self::PRODUCT_NAME;
    }

    public function price(): float
    {
        return self::PRODUCT_PRICE;
    }

    public function checkPrice(float $moneyInserted): void
    {
        if ($moneyInserted < $this->price()) {
            throw new \InvalidArgumentException('Insufficient money inserted');
        }
    }
}
