<?php

namespace App\Application\Service;

class ServiceResponse
{
    private array $itemsUpdated;
    private array $changeUpdated;

    public function __construct(array $itemsUpdated, array $changeUpdated)
    {
        $this->itemsUpdated = $itemsUpdated;
        $this->changeUpdated = $changeUpdated;
    }

    public function itemsUpdated(): array
    {
        return $this->itemsUpdated;
    }

    public function changeUpdated(): array
    {
        return $this->changeUpdated;
    }
}
