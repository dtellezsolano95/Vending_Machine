<?php

namespace App\Infrastructure\Persistence\Product;

use App\Domain\Product\StockRepositoryInterface;

class JsonStockRepository implements StockRepositoryInterface
{
    private const STOCK_FILE = __DIR__ . '/../../../var/stock.json';

    public function setStock(string $productCode, int $count): void
    {
        $this->ensureDirectoryExists();
        
        $stock = $this->loadStock();
        $stock[$productCode] = $count;
        
        file_put_contents(self::STOCK_FILE, json_encode($stock, JSON_PRETTY_PRINT));
    }

    public function hasStock(string $productCode): bool
    {
        $stock = $this->loadStock();

        return isset($stock[$productCode]) && $stock[$productCode] > 0;
    }

    public function getStock(string $productCode): int
    {
        $stock = $this->loadStock();

        return $stock[$productCode] ?? 0;
    }

    public function decreaseStock(string $productCode): void
    {
        $this->ensureDirectoryExists();
        
        $stock = $this->loadStock();
        $currentStock = $stock[$productCode] ?? 0;
        $stock[$productCode] = max(0, $currentStock - 1);
        
        file_put_contents(self::STOCK_FILE, json_encode($stock, JSON_PRETTY_PRINT));
    }

    private function loadStock(): array
    {
        if (!file_exists(self::STOCK_FILE)) {
            return [];
        }

        $content = file_get_contents(self::STOCK_FILE);
        if ($content === false || empty(trim($content))) {
            return [];
        }

        $stock = json_decode($content, true);
        return is_array($stock) ? $stock : [];
    }

    private function ensureDirectoryExists(): void
    {
        $dir = dirname(self::STOCK_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
