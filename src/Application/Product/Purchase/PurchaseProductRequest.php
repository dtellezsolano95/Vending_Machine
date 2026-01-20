<?php

namespace App\Application\Product\Purchase;

use InvalidArgumentException;

class PurchaseProductRequest
{
    private string $product;

    public function __construct(?string $product)
    {
        if (empty($product)) {
            throw new InvalidArgumentException('Product field is required');
        }
        
        $this->product = $product;
    }

    public function product(): string
    {
        return $this->product;
    }
}
