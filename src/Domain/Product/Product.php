<?php

namespace App\Domain\Product;

final class Product
{
    private const SODA = 'SODA';
    private const JUICE = 'JUICE';
    private const WATER = 'WATER';

    public function __construct(
        private readonly ProductName $name
    ) {
    }

    public static function create(ProductName $name): self
    {
        $product = new self(
            $name
        );

        return $product;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function price(): float
    {
        if ($this->name->value() === self::WATER) {
            return 0.65;
        }

        if ($this->name->value() === self::JUICE) {
            return 1.00;
        }

        if ($this->name->value() === self::SODA) {
            return 1.50;
        }

        return 0.0;
    }

    public function checkPrice(float $moneyInserted): void
    {
        if ($moneyInserted < $this->price()) {
            throw new \InvalidArgumentException('Insufficient money inserted');
        }    
    }
}
