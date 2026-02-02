<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InsertMoneyAcceptanceTest extends WebTestCase
{
    private const API_ENDPOINT = '/api/money/insert';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->resetMachineState();
    }

    /**
     * @test
     */
    public function shouldInsertValidCoinSuccessfully(): void
    {
        // Arrange
        $client = static::createClient();
        $coinValue = 0.25;

        // Act
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT,
            ['coin' => $coinValue]
        );

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('coin_inserted', $responseData);
        $this->assertArrayHasKey('current_balance', $responseData);
        $this->assertEquals($coinValue, $responseData['coin_inserted']);
        $this->assertEquals($coinValue, $responseData['current_balance']);
    }

    /**
     * @test
     */
    public function shouldAccumulateBalanceWhenInsertingMultipleCoins(): void
    {
        // Arrange
        $client = static::createClient();

        $firstCoin = 0.25;
        $secondCoin = 0.10;

        $expectedBalance = 0.35;

        // Act - Insert first coin
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT,
            ['coin' => $firstCoin]
        );

        $this->assertResponseIsSuccessful();

        // Act - Insert second coin
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT,
            ['coin' => $secondCoin]
        );

        // Assert
        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertEquals($secondCoin, $responseData['coin_inserted']);
        $this->assertEquals($expectedBalance, $responseData['current_balance']);
    }

    /**
     * @test
     */
    public function shouldAcceptAllValidCoins(): void
    {
        $validCoins = [0.05, 0.10, 0.25, 1.00];
        
        // Arrange
        $client = static::createClient();
        
        foreach ($validCoins as $coinValue) {
            $this->resetMachineState();

            // Act
            $client->jsonRequest(
                'POST',
                self::API_ENDPOINT,
                ['coin' => $coinValue]
            );

            // Assert
            $this->assertResponseIsSuccessful();
            
            $responseData = json_decode($client->getResponse()->getContent(), true);
            $this->assertEquals($coinValue, $responseData['coin_inserted']);
            $this->assertEquals($coinValue, $responseData['current_balance']);
        }
    }

    /**
     * @test
     */
    public function shouldRejectInvalidCoinValue(): void
    {
        // Arrange
        $client = static::createClient();
        $invalidCoin = 0.15;

        // Act
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT,
            ['coin' => $invalidCoin]
        );

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Invalid coin value', $responseData['message']);
    }

    /**
     * @test
     */
    public function shouldRejectNegativeCoinValue(): void
    {
        // Arrange
        $client = static::createClient();
        $negativeCoin = -0.25;

        // Act
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT,
            ['coin' => $negativeCoin]
        );

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('message', $responseData);
    }

    /**
     * @test
     */
    public function shouldRejectRequestWithoutCoinField(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT,
            []
        );

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Missing required field: coin', $responseData['message']);
    }

    /**
     * @test
     */
    public function shouldInsertMultipleCoinsInSequence(): void
    {
        // Arrange
        $client = static::createClient();
        $coins = [0.25, 0.25, 0.10, 0.05, 1.00];
        $expectedBalances = [0.25, 0.50, 0.60, 0.65, 1.65];

        // Act & Assert
        foreach ($coins as $index => $coin) {
            $client->jsonRequest(
                'POST',
                self::API_ENDPOINT,
                ['coin' => $coin]
            );

            $this->assertResponseIsSuccessful();
            
            $responseData = json_decode($client->getResponse()->getContent(), true);
            
            $this->assertEquals($coin, $responseData['coin_inserted']);
            $this->assertEquals(
                $expectedBalances[$index], 
                $responseData['current_balance'],
                "Balance mismatch after inserting coin #{$index}"
            );
        }
    }

    private function resetMachineState(): void
    {
        $userMoneyFile = __DIR__ . '/../../src/var/user_money.json';
        
        if (file_exists($userMoneyFile)) {
            unlink($userMoneyFile);
        }
    }
}
