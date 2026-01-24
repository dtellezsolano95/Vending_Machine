<?php

namespace App\Application\Money\Insert;

use App\Domain\Money\Coin;
use App\Domain\Money\UserMoneyRepositoryInterface;

class InsertMoneyUseCase
{
    private UserMoneyRepositoryInterface $moneyRepository;

    public function __construct(UserMoneyRepositoryInterface $moneyRepository)
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
