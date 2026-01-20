<?php

namespace App\Domain\Money;

interface MoneyRepositoryInterface
{
    public function saveCoin(Coin $coin): void;

    public function getCurrentBalance(): float;

    public function getInsertedCoins(): array;

    public function clearCoins(): void;
}
