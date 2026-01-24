<?php

namespace App\Application\Product\Purchase;

use App\Domain\Money\Exception\InsufficientChangeException;
use App\Domain\Money\MachineMoneyRepositoryInterface;
use App\Domain\Money\UserMoneyRepositoryInterface;
use App\Domain\Product\Exception\InsufficientStockException;
use App\Domain\Product\ProductFactory;
use App\Domain\Product\StockRepositoryInterface;

class PurchaseProductUseCase
{
    private const AVAILABLE_RETURN_COINS = [0.25, 0.10, 0.05];

    private StockRepositoryInterface $stockRepository;
    private UserMoneyRepositoryInterface $userMoneyRepository;
    private MachineMoneyRepositoryInterface $machineMoneyRepository;

    public function __construct(
        StockRepositoryInterface $stockRepository,
        UserMoneyRepositoryInterface $userMoneyRepository,
        MachineMoneyRepositoryInterface $machineMoneyRepository
    ) {
        $this->stockRepository = $stockRepository;
        $this->userMoneyRepository = $userMoneyRepository;
        $this->machineMoneyRepository = $machineMoneyRepository;
    }

    public function execute(PurchaseProductRequest $request): PurchaseProductResponse
    {
        $product = ProductFactory::create($request->product());

        $this->validateStockAvailability($product->name());

        $moneyInserted = $this->userMoneyRepository->getCurrentBalance();

        $product->checkPrice($moneyInserted);

        $change = $this->calculateChange($moneyInserted, $product->price());

        $this->validateChangeAvailability($change, $moneyInserted, $product->price());

        $this->processTransaction($product->name(), $change);

        return new PurchaseProductResponse(
            $product->name(),
            $change
        );
    }

    private function validateStockAvailability(string $productName): void
    {
        if (!$this->stockRepository->hasStock($productName)) {
            throw InsufficientStockException::forProduct($productName);
        }
    }

    private function validateChangeAvailability(array $change, float $moneyInserted, float $productPrice): void
    {
        if (empty($change)) {
            return;
        }

        if (!$this->machineMoneyRepository->hasEnoughChange($change)) {
            throw InsufficientChangeException::forAmount($moneyInserted - $productPrice);
        }
    }

    private function processTransaction(string $productName, array $change): void
    {
        $this->stockRepository->decreaseStock($productName);

        $this->userMoneyRepository->clearCoins();
        
        $this->machineMoneyRepository->decreaseChangeCoins($change);
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
