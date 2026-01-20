<?php

namespace App\Domain\Product;

interface ProductInterface
{
    public function name(): string;
    
    public function price(): float;
    
    public function checkPrice(float $moneyInserted): void;
}
