<?php

namespace App\Application\Product\Get;

use App\Domain\Money\MoneyRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductRepositoryInterface;

class GetProductUseCase
{
    private ProductRepositoryInterface $productRepository;
    private MoneyRepositoryInterface $moneyRepository;
    public function __construct(ProductRepositoryInterface $productRepository, MoneyRepositoryInterface $moneyRepository)
    {
        $this->productRepository = $productRepository;
        $this->moneyRepository = $moneyRepository;
    }

    public function execute(string $product): GetProductResponse
    {
        $productName = new ProductName($product);
        $product = Product::create($productName);

        $moneyInserted = $this->moneyRepository->getCurrentBalance();

        $product->checkPrice($moneyInserted);

        $change = $product->calculateChange($moneyInserted);

        $this->moneyRepository->clearCoins();

        return new GetProductResponse(
            $product->name()->value(),
            $change
        );
    }
}
