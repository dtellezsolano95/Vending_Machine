<?php

namespace App\Domain\Product;

final class ProductFactory
{
    public static function create(string $productName): ProductInterface
    {
        return match ($productName) {
            'WATER' => new WaterProduct(),
            'JUICE' => new JuiceProduct(),
            'SODA' => new SodaProduct(),
            default => throw new \InvalidArgumentException(
                sprintf('Product "%s" does not exist', $productName)
            ),
        };
    }
}
