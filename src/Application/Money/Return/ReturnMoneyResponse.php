<?php

namespace App\Application\Money\Return;

class ReturnMoneyResponse
{
    private array $coinsReturned;

    public function __construct(array $coinsReturned)
    {
        $this->coinsReturned = $coinsReturned;
    }

    public function coinsReturned(): array
    {
        return $this->coinsReturned;
    }
}
