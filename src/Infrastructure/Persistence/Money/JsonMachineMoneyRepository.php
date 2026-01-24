<?php

namespace App\Infrastructure\Persistence\Money;

use App\Domain\Money\MachineMoneyRepositoryInterface;

class JsonMachineMoneyRepository implements MachineMoneyRepositoryInterface
{
    private const CHANGE_FILE = __DIR__ . '/../../../var/machine_change.json';

    public function setChangeCoins(float $coinValue, int $count): void
    {
        $this->ensureDirectoryExists();
        
        $change = $this->loadChange();
        $key = (string) $coinValue;
        $change[$key] = $count;
        
        file_put_contents(self::CHANGE_FILE, json_encode($change, JSON_PRETTY_PRINT));
    }

    public function getChangeCoins(): array
    {
        return $this->loadChange();
    }

    public function hasEnoughChange(array $requiredChange): bool
    {
        $availableChange = $this->loadChange();

        $requiredCoinsCount = $this->countRequiredCoins($requiredChange);

        return $this->validateAvailableChange($availableChange, $requiredCoinsCount);
    }

    private function countRequiredCoins(array $requiredChange): array
    {
        $requiredCoinsCount = [];

        foreach ($requiredChange as $coinValue) {
            $key = (string) $coinValue;
            $requiredCoinsCount[$key] = ($requiredCoinsCount[$key] ?? 0) + 1;
        }

        return $requiredCoinsCount;
    }

    private function validateAvailableChange(array $availableChange, array $requiredCoinsCount): bool
    {
        foreach ($requiredCoinsCount as $coinKey => $count) {
            $available = $availableChange[$coinKey] ?? 0;
            if ($available < $count) {
                return false;
            }
        }

        return true;
    }

    public function decreaseChangeCoins(array $change): void
    {
        if (empty($change)) {
            return;
        }

        $this->ensureDirectoryExists();
        
        $availableChange = $this->loadChange();
        
        foreach ($change as $coinValue) {
            $key = (string) $coinValue;
            if (isset($availableChange[$key])) {
                $availableChange[$key] = max(0, $availableChange[$key] - 1);
            }
        }
        
        file_put_contents(self::CHANGE_FILE, json_encode($availableChange, JSON_PRETTY_PRINT));
    }

    private function loadChange(): array
    {
        if (!file_exists(self::CHANGE_FILE)) {
            return [];
        }

        $content = file_get_contents(self::CHANGE_FILE);
        if ($content === false || empty(trim($content))) {
            return [];
        }

        $change = json_decode($content, true);
        return is_array($change) ? $change : [];
    }

    private function ensureDirectoryExists(): void
    {
        $dir = dirname(self::CHANGE_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
