<?php

namespace App\Infrastructure\Persistence\Money;

use App\Domain\Money\Coin;
use App\Domain\Money\UserMoneyRepositoryInterface;

class JsonUserMoneyRepository implements UserMoneyRepositoryInterface
{
    private const MONEY_FILE = __DIR__ . '/../../../var/user_money.json';

    public function saveCoin(Coin $coin): void
    {
        $this->ensureDirectoryExists();
        
        $coins = $this->loadCoins();
        $coins[] = $coin->value();
        
        file_put_contents(self::MONEY_FILE, json_encode($coins, JSON_PRETTY_PRINT));
    }

    public function getCurrentBalance(): float
    {
        $coins = $this->loadCoins();
        return array_sum($coins);
    }

    public function getInsertedCoins(): array
    {
        $coins = $this->loadCoins();
        return array_map(fn($value) => new Coin($value), $coins);
    }

    public function clearCoins(): void
    {
        if (file_exists(self::MONEY_FILE)) {
            unlink(self::MONEY_FILE);
        }
    }

    private function loadCoins(): array
    {
        if (!file_exists(self::MONEY_FILE)) {
            return [];
        }

        $content = file_get_contents(self::MONEY_FILE);
        if ($content === false || empty(trim($content))) {
            return [];
        }

        $coins = json_decode($content, true);
        return is_array($coins) ? $coins : [];
    }

    private function ensureDirectoryExists(): void
    {
        $dir = dirname(self::MONEY_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
