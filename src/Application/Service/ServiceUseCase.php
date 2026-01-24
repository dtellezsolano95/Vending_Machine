<?php

namespace App\Application\Service;

use App\Domain\Money\Coin;
use App\Domain\Money\MachineMoneyRepositoryInterface;
use App\Domain\Product\ProductFactory;
use App\Domain\Product\StockRepositoryInterface;

class ServiceUseCase
{
    private StockRepositoryInterface $stockRepository;
    private MachineMoneyRepositoryInterface $machineMoneyRepository;

    public function __construct(
        StockRepositoryInterface $stockRepository,
        MachineMoneyRepositoryInterface $machineMoneyRepository
    ) {
        $this->stockRepository = $stockRepository;
        $this->machineMoneyRepository = $machineMoneyRepository;
    }

    public function execute(ServiceRequest $request): ServiceResponse
    {
        $itemsUpdated = $this->updateProductStock($request->items());
        $changeUpdated = $this->updateChangeCoins($request->change());

        return new ServiceResponse($itemsUpdated, $changeUpdated);
    }

    private function updateProductStock(array $items): array
    {
        $itemsUpdated = [];

        foreach ($items as $item) {
            $count = (int) $item['count'];
            
            $product = ProductFactory::create($item['code']);
            
            $this->stockRepository->setStock($product->name(), $count);
            
            $itemsUpdated[] = [
                'code' => $product->name(),
                'count' => $count
            ];
        }

        return $itemsUpdated;
    }

    private function updateChangeCoins(array $change): array
    {
        $changeUpdated = [];

        foreach ($change as $coin) {
            $coinValue = (float) $coin['value'];
            $count = (int) $coin['count'];
            
            $coinInstance = new Coin($coinValue);
            
            $this->machineMoneyRepository->setChangeCoins($coinInstance->value(), $count);
            
            $changeUpdated[] = [
                'value' => $coinInstance->value(),
                'count' => $count
            ];
        }

        return $changeUpdated;
    }
}
