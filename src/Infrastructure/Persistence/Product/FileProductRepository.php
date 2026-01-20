<?php

namespace App\Infrastructure\Persistence\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;

class FileProductRepository implements ProductRepositoryInterface
{
    private const PRODUCT_FILE = __DIR__ . '/../../../var/products.txt';

    public function addProduct(Product $product): void
    {
        $this->ensureDirectoryExists();
        
        file_put_contents(self::PRODUCT_FILE, $product->name() . "\n", FILE_APPEND);
    }

    private function ensureDirectoryExists(): void
    {
        $dir = dirname(self::PRODUCT_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
