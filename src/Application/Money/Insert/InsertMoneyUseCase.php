<?php

namespace App\Application\Money\Insert;

use App\Domain\Money\Coin;
use App\Domain\Money\MoneyRepositoryInterface;

class InsertMoneyUseCase
{
    private MoneyRepositoryInterface $moneyRepository;

    public function __construct(MoneyRepositoryInterface $moneyRepository)
    {
        $this->moneyRepository = $moneyRepository;
    }

    public function execute(InsertMoneyRequest $request): InsertMoneyResponse
    {
        $coin = new Coin($request->coinValue());
        
        $this->moneyRepository->saveCoin($coin);
        
        $currentBalance = $this->moneyRepository->getCurrentBalance();

        return new InsertMoneyResponse(
            $coin->value(),
            $currentBalance
        );
    }
}
