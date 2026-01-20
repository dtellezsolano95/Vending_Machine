<?php

namespace App\Application\Product\Get;

class GetProductResponse
{
    private string $productName;
    private array $changeReturned;

    public function __construct(string $productName, array $changeReturned)
    {
        $this->productName = $productName;
        $this->changeReturned = $changeReturned;
    }

    public function productName(): string
    {
        return $this->productName;
    }

    public function changeReturned(): array
    {
        return $this->changeReturned;
    }
}
