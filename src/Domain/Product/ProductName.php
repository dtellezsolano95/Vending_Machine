<?php

namespace App\Domain\Product;

final class ProductName
{
    private const VALID_NAMES = ['WATER', 'JUICE', 'SODA'];

    private string $value;

    public function __construct(string $name) {
        $this->ensureIsValid($name);

        $this->value = $name;
    }

    public function value(): string
    {
        if ($this->value === null) {
            return '';
        }

        return $this->value;
    }

    private function ensureIsValid(string $name): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Product name cannot be empty');
        }
        
        if (!in_array($name, self::VALID_NAMES, true)) {
            throw new \InvalidArgumentException('Product name must be WATER, JUICE or SODA');
        }
    }
}
