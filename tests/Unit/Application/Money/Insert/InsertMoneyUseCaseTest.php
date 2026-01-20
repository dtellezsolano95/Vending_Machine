<?php

namespace App\Tests\Unit\Application\Money\Insert;

use App\Application\Money\Insert\InsertMoneyRequest;
use App\Application\Money\Insert\InsertMoneyUseCase;
use App\Domain\Money\Coin;
use App\Domain\Money\Exception\InvalidCoinValueException;
use App\Domain\Money\MoneyRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InsertMoneyUseCaseTest extends TestCase
{
    private MoneyRepositoryInterface&MockObject $moneyRepository;
    private InsertMoneyUseCase $useCase;

    protected function setUp(): void
    {
        $this->moneyRepository = $this->createMock(MoneyRepositoryInterface::class);
        $this->useCase = new InsertMoneyUseCase($this->moneyRepository);
    }

    public function testShouldInsertValidCoinAndReturnsCorrectResponse(): void
    {
        // Arrange
        $coinValue = 0.25;
        $expectedBalance = 0.25;
        
        $request = new InsertMoneyRequest(['coin' => $coinValue]);
        $coin = new Coin($coinValue);
        
        $this->moneyRepository
            ->expects($this->once())
            ->method('saveCoin')
            ->with($coin);
        
        $this->moneyRepository
            ->expects($this->once())
            ->method('getCurrentBalance')
            ->willReturn($expectedBalance);

        // Act
        $response = $this->useCase->execute($request);

        // Assert
        $this->assertSame($coinValue, $response->coinInserted());
        $this->assertSame($expectedBalance, $response->currentBalance());
    }

    public function testShouldAccumulatesMultipleCoinsInBalance(): void
    {
        // Arrange
        $firstCoinValue = 0.25;
        $secondCoinValue = 0.10;
        $expectedBalanceAfterFirst = 0.25;
        $expectedBalanceAfterSecond = 0.35;
        
        $firstRequest = new InsertMoneyRequest(['coin' => $firstCoinValue]);
        $secondRequest = new InsertMoneyRequest(['coin' => $secondCoinValue]);
        
        $this->moneyRepository
            ->expects($this->exactly(2))
            ->method('saveCoin');
        
        $this->moneyRepository
            ->expects($this->exactly(2))
            ->method('getCurrentBalance')
            ->willReturnOnConsecutiveCalls($expectedBalanceAfterFirst, $expectedBalanceAfterSecond);

        // Act
        $firstResponse = $this->useCase->execute($firstRequest);
        $secondResponse = $this->useCase->execute($secondRequest);

        // Assert
        $this->assertSame($firstCoinValue, $firstResponse->coinInserted());
        $this->assertSame($expectedBalanceAfterFirst, $firstResponse->currentBalance());
        
        $this->assertSame($secondCoinValue, $secondResponse->coinInserted());
        $this->assertSame($expectedBalanceAfterSecond, $secondResponse->currentBalance());
    }

    public static function validCoinsProvider(): array
    {
        return [
            'nickel' => [0.05],
            'dime' => [0.10],
            'quarter' => [0.25],
            'dollar' => [1.00],
        ];
    }

    /**
    * @dataProvider validCoinsProvider
    */
    public function testShouldAcceptsAllValidCoins(float $coinValue): void
    {
        // Arrange
        $request = new InsertMoneyRequest(['coin' => $coinValue]);
        
        $this->moneyRepository
            ->expects($this->once())
            ->method('saveCoin');
        
        $this->moneyRepository
            ->expects($this->once())
            ->method('getCurrentBalance')
            ->willReturn($coinValue);

        // Act
        $response = $this->useCase->execute($request);

        // Assert
        $this->assertSame($coinValue, $response->coinInserted());
        $this->assertSame($coinValue, $response->currentBalance());
    }

    public static function invalidCoinsProvider(): array
    {
        return [
            'penny' => [0.01],
            'two dollars' => [2.00],
            'fifty cents' => [0.50],
            'negative value' => [-0.25],
            'zero' => [0.00],
        ];
    }

    /**
    * @dataProvider invalidCoinsProvider
    */
    public function testShouldRejectsInvalidCoins(float $coinValue): void
    {
        // Assert
        $this->expectException(InvalidCoinValueException::class);

        // Act
        $request = new InsertMoneyRequest(['coin' => $coinValue]);
        $this->useCase->execute($request);
    }

    public function testShouldThrowsExceptionWhenCoinFieldIsMissingInRequest(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: coin');

        // Act
        new InsertMoneyRequest([]);
    }
}
