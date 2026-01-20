<?php

namespace App\Application\Money\Insert;

use InvalidArgumentException;

class InsertMoneyRequest
{
    private float $coinValue;

    public function __construct(array $data)
    {
        $this->validate($data);
        $this->coinValue = (float) $data['coin'];
    }

    private function validate(array $data): void
    {
        if (!isset($data['coin'])) {
            throw new InvalidArgumentException('Missing required field: coin');
        }
    }

    public function coinValue(): float
    {
        return $this->coinValue;
    }
}
