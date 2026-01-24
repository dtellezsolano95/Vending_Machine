<?php

namespace App\Domain\Product;

final class Product
{
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
}
