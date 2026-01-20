<?php

namespace App\Domain\Product;

interface ProductRepositoryInterface
{
    public function addProduct(Product $product): void;
}
