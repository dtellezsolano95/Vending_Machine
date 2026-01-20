<?php

namespace App\Application\Money\Insert;

class InsertMoneyResponse
{
    private float $coinInserted;
    private float $currentBalance;

    public function __construct(float $coinInserted, float $currentBalance)
    {
        $this->coinInserted = $coinInserted;
        $this->currentBalance = $currentBalance;
    }

    public function coinInserted(): float
    {
        return $this->coinInserted;
    }

    public function currentBalance(): float
    {
        return $this->currentBalance;
    }
}
