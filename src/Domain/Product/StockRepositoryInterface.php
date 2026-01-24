<?php

namespace App\Domain\Product;

interface StockRepositoryInterface
{
    public function setStock(string $productCode, int $count): void;

    public function hasStock(string $productCode): bool;

    public function getStock(string $productCode): int;

    public function decreaseStock(string $productCode): void;
}
