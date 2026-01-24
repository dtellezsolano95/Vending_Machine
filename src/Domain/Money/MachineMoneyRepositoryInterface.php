<?php

namespace App\Domain\Money;

interface MachineMoneyRepositoryInterface
{
    public function setChangeCoins(float $coinValue, int $count): void;

    public function getChangeCoins(): array;

    public function hasEnoughChange(array $requiredChange): bool;

    public function decreaseChangeCoins(array $change): void;
}
