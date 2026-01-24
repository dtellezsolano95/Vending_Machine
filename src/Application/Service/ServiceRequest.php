<?php

namespace App\Application\Service;

use InvalidArgumentException;

class ServiceRequest
{
    private array $items;
    private array $change;

    public function __construct(array $data)
    {
        $this->validate($data);
        $this->items = $data['items'] ?? [];
        $this->change = $data['change'] ?? [];
    }

    private function validate(array $data): void
    {
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new InvalidArgumentException('Missing or invalid field: items');
        }

        if (!isset($data['change']) || !is_array($data['change'])) {
            throw new InvalidArgumentException('Missing or invalid field: change');
        }

        foreach ($data['items'] as $item) {
            if (!isset($item['code']) || !isset($item['count'])) {
                throw new InvalidArgumentException('Each item must have code and count fields');
            }

            if (!is_string($item['code'])) {
                throw new InvalidArgumentException('Item code must be a string');
            }

            if (!is_numeric($item['count']) || $item['count'] < 0) {
                throw new InvalidArgumentException('Item count must be a non-negative number');
            }
        }

        foreach ($data['change'] as $coin) {
            if (!isset($coin['value']) || !isset($coin['count'])) {
                throw new InvalidArgumentException('Each change coin must have value and count fields');
            }

            if (!is_numeric($coin['value']) || $coin['value'] <= 0) {
                throw new InvalidArgumentException('Coin value must be a positive number');
            }

            if (!is_numeric($coin['count']) || $coin['count'] < 0) {
                throw new InvalidArgumentException('Coin count must be a non-negative number');
            }
        }
    }

    public function items(): array
    {
        return $this->items;
    }

    public function change(): array
    {
        return $this->change;
    }
}
