<?php

namespace App\Domain\Product;

final class WaterProduct implements ProductInterface
{
    private const PRODUCT_NAME = 'WATER';
    private const PRODUCT_PRICE = 0.65;

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
