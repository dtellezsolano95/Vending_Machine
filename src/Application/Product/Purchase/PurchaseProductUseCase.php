<?php

namespace App\Application\Product\Purchase;

use App\Domain\Money\MoneyRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductRepositoryInterface;

class PurchaseProductUseCase
{
    private const AVAILABLE_RETURN_COINS = [0.25, 0.10, 0.05];

    private ProductRepositoryInterface $productRepository;
    private MoneyRepositoryInterface $moneyRepository;
    public function __construct(ProductRepositoryInterface $productRepository, MoneyRepositoryInterface $moneyRepository)
    {
        $this->productRepository = $productRepository;
        $this->moneyRepository = $moneyRepository;
    }

    public function execute(PurchaseProductRequest $request): PurchaseProductResponse
    {
        $productName = new ProductName($request->product());
        $product = Product::create($productName);

        $moneyInserted = $this->moneyRepository->getCurrentBalance();

        $product->checkPrice($moneyInserted);

        $change = $this->calculateChange($moneyInserted, $product->price());

        $this->moneyRepository->clearCoins();

        return new PurchaseProductResponse(
            $product->name()->value(),
            $change
        );
    }

    private function calculateChange(float $moneyInserted, float $productPrice): array
    {
        $change = [];

        $difference = $moneyInserted - $productPrice;
        
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
