<?php

namespace App\Tests\Unit\Application\Money\Return;

use App\Application\Money\Return\ReturnMoneyUseCase;
use App\Domain\Money\Coin;
use App\Domain\Money\UserMoneyRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReturnMoneyUseCaseTest extends TestCase
{
    private UserMoneyRepositoryInterface&MockObject $userMoneyRepository;
    private ReturnMoneyUseCase $useCase;

    protected function setUp(): void
    {
        $this->userMoneyRepository = $this->createMock(UserMoneyRepositoryInterface::class);
        $this->useCase = new ReturnMoneyUseCase($this->userMoneyRepository);
    }

    public function testShouldReturnInsertedCoinsAndClearBalance(): void
    {
        // Arrange
        $expectedCoins = [
            new Coin(0.25),
            new Coin(0.10),
            new Coin(1.00)
        ];
        
        $this->userMoneyRepository
            ->expects($this->once())
            ->method('getInsertedCoins')
            ->willReturn($expectedCoins);
        
        $this->userMoneyRepository
            ->expects($this->once())
            ->method('clearCoins');

        // Act
        $response = $this->useCase->execute();

        // Assert
        $this->assertSame([0.25, 0.10, 1.00], $response->coinsReturned());
    }

    public function testShouldReturnEmptyArrayWhenNoCoinsInserted(): void
    {
        // Arrange
        $this->userMoneyRepository
            ->expects($this->once())
            ->method('getInsertedCoins')
            ->willReturn([]);
        
        $this->userMoneyRepository
            ->expects($this->once())
            ->method('clearCoins');

        // Act
        $response = $this->useCase->execute();

        // Assert
        $this->assertSame([], $response->coinsReturned());
        $this->assertEmpty($response->coinsReturned());
    }

    public static function coinCombinationsProvider(): array
    {
        return [
            'combination1' => [[new Coin(0.05)], [0.05]],
            'combination2' => [[new Coin(0.10)], [0.10]],
            'combination3' => [[new Coin(0.25)], [0.25]],
            'combination4' => [[new Coin(1.00)], [1.00]],
            'combination5' => [[new Coin(0.25), new Coin(0.25)], [0.25, 0.25]],
            'combination6' => [
                [new Coin(1.00), new Coin(0.25), new Coin(0.10), new Coin(0.05)],
                [1.00, 0.25, 0.10, 0.05]
            ],
        ];
    }

    /**
    * @dataProvider coinCombinationsProvider
    */
    public function testShouldHandleVariousCoinCombinations(array $coins, array $expectedValues): void
    {
        // Arrange
        $this->userMoneyRepository
            ->expects($this->once())
            ->method('getInsertedCoins')
            ->willReturn($coins);
        
        $this->userMoneyRepository
            ->expects($this->once())
            ->method('clearCoins');

        // Act
        $response = $this->useCase->execute();

        // Assert
        $this->assertSame($expectedValues, $response->coinsReturned());
    }
}
