<?php

namespace App\Domain\Money;

class ChangeCalculator
{
    private const AVAILABLE_CHANGE_COINS = [0.25, 0.10, 0.05];

    public function calculate(float $amountPaid, float $productPrice): array
    {
        $changeAmount = $amountPaid - $productPrice;
        
        if ($changeAmount <= 0) {
            return [];
        }

        return $this->distributeChangeInCoins($changeAmount);
    }

    public function availableCoins(): array
    {
        return self::AVAILABLE_CHANGE_COINS;
    }

    private function distributeChangeInCoins(float $changeAmount): array
    {
        $coins = [];
        $remainingAmount = round($changeAmount, 2);

        foreach (self::AVAILABLE_CHANGE_COINS as $coinValue) {
            while ($this->canUseCoin($remainingAmount, $coinValue)) {
                $coins[] = $coinValue;
                $remainingAmount = round($remainingAmount - $coinValue, 2);
            }
        }

        return $coins;
    }

    private function canUseCoin(float $remainingAmount, float $coinValue): bool
    {
        return $remainingAmount >= $coinValue - 0.001;
    }
}
