<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Money\Coin;
use App\Domain\Money\MoneyRepositoryInterface;

class FileMoneyRepository implements MoneyRepositoryInterface
{
    private const MONEY_FILE = __DIR__ . '/../../../var/money.txt';

    public function saveCoin(Coin $coin): void
    {
        $this->ensureDirectoryExists();
        
        file_put_contents(self::MONEY_FILE, $coin->value() . "\n", FILE_APPEND);
    }

    public function getCurrentBalance(): float
    {
        if (!file_exists(self::MONEY_FILE)) {
            return 0.0;
        }

        $content = file_get_contents(self::MONEY_FILE);
        if ($content === false || empty(trim($content))) {
            return 0.0;
        }

        $coins = array_filter(explode("\n", trim($content)));
        return array_sum(array_map('floatval', $coins));
    }

    public function getInsertedCoins(): array
    {
        if (!file_exists(self::MONEY_FILE)) {
            return [];
        }

        $content = file_get_contents(self::MONEY_FILE);
        if ($content === false || empty(trim($content))) {
            return [];
        }

        $coins = array_filter(explode("\n", trim($content)));
        return array_map(fn($value) => new Coin(floatval($value)), $coins);
    }

    public function clearCoins(): void
    {
        if (file_exists(self::MONEY_FILE)) {
            unlink(self::MONEY_FILE);
        }
    }

    private function ensureDirectoryExists(): void
    {
        $dir = dirname(self::MONEY_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
