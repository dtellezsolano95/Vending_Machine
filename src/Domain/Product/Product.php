<?php

namespace App\Domain\Product;

final class Product
{
    private const SODA = 'SODA';
    private const JUICE = 'JUICE';
    private const WATER = 'WATER';

    private const AVAILABLE_RETURN_COINS = [0.25, 0.10, 0.05];

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

    public function calculateChange(float $moneyInserted): array
    {
        $change = [];

        $difference = $moneyInserted - $this->price();
        
        $remaining = round($difference, 2);
        
        foreach (self::AVAILABLE_RETURN_COINS as $coin) {
            while ($remaining >= $coin - 0.001) {
                $change[] = $coin;
                $remaining = round($remaining - $coin, 2);
            }
        }
        
        return $change;
    }
}
