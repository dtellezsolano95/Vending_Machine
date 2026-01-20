<?php

namespace App\Application\Money\Return;

use App\Domain\Money\Coin;
use App\Domain\Money\MoneyRepositoryInterface;

class ReturnMoneyUseCase
{
    private MoneyRepositoryInterface $moneyRepository;

    public function __construct(MoneyRepositoryInterface $moneyRepository)
    {
        $this->moneyRepository = $moneyRepository;
    }

    public function execute(): ReturnMoneyResponse
    {
        $insertedCoins = $this->moneyRepository->getInsertedCoins();

        $this->moneyRepository->clearCoins();

        return new ReturnMoneyResponse(
            array_map(
                fn (Coin $coin) => $coin->value(),
                $insertedCoins
            )
        );
    }
}
