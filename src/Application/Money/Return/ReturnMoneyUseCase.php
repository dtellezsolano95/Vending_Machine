<?php

namespace App\Application\Money\Return;

use App\Domain\Money\Coin;
use App\Domain\Money\UserMoneyRepositoryInterface;

class ReturnMoneyUseCase
{
    private UserMoneyRepositoryInterface $moneyRepository;

    public function __construct(UserMoneyRepositoryInterface $moneyRepository)
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
