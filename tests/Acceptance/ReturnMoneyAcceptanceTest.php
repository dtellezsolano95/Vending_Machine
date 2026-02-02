<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReturnMoneyAcceptanceTest extends WebTestCase
{
    private const API_ENDPOINT_RETURN = '/api/money/return';
    private const API_ENDPOINT_INSERT = '/api/money/insert';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->resetMachineState();
    }

    /**
    * @test
    */
    public function shouldReturnEmptyArrayWhenNoMoneyInserted(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_RETURN,
            []
        );

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('coins_returned', $responseData);
        $this->assertIsArray($responseData['coins_returned']);
        $this->assertEmpty($responseData['coins_returned']);
    }

    /**
    * @test
    */
    public function shouldReturnSingleInsertedCoin(): void
    {
        // Arrange
        $client = static::createClient();
        $coinValue = 0.25;

        // Insert coin first
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_INSERT,
            ['coin' => $coinValue]
        );

        $this->assertResponseIsSuccessful();

        // Act - Return the money
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_RETURN,
            []
        );

        // Assert
        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('coins_returned', $responseData);
        $this->assertIsArray($responseData['coins_returned']);
        $this->assertCount(1, $responseData['coins_returned']);
        $this->assertEquals($coinValue, $responseData['coins_returned'][0]);
    }

    /**
    * @test
    */
    public function shouldReturnMultipleInsertedCoins(): void
    {
        // Arrange
        $client = static::createClient();
        $coinsToInsert = [0.25, 0.10, 0.05, 1.00];

        // Insert multiple coins
        foreach ($coinsToInsert as $coin) {
            $client->jsonRequest(
                'POST',
                self::API_ENDPOINT_INSERT,
                ['coin' => $coin]
            );

            $this->assertResponseIsSuccessful();
        }

        // Act - Return all the money
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_RETURN,
            []
        );

        // Assert
        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('coins_returned', $responseData);
        $this->assertIsArray($responseData['coins_returned']);
        $this->assertCount(count($coinsToInsert), $responseData['coins_returned']);
        
        $returnedCoins = $responseData['coins_returned'];
        sort($coinsToInsert);
        sort($returnedCoins);
        $this->assertEquals($coinsToInsert, $returnedCoins);
    }

    /**
    * @test
    */
    public function shouldClearBalanceAfterReturningMoney(): void
    {
        // Arrange
        $client = static::createClient();
        $coinValue = 0.25;

        // Insert a coin
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_INSERT,
            ['coin' => $coinValue]
        );

        $this->assertResponseIsSuccessful();

        // Act - Return the money
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_RETURN,
            []
        );

        $this->assertResponseIsSuccessful();

        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_RETURN,
            []
        );

        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('coins_returned', $responseData);
        $this->assertEmpty($responseData['coins_returned']);
    }

    /**
    * @test
    */
    public function shouldReturnLargeAmountCorrectly(): void
    {
        // Arrange
        $client = static::createClient();
        $coinsToInsert = [1.00, 1.00, 1.00, 0.25, 0.25, 0.10, 0.10, 0.05];
        $expectedTotal = 3.75;

        // Insert multiple coins
        foreach ($coinsToInsert as $coin) {
            $client->jsonRequest(
                'POST',
                self::API_ENDPOINT_INSERT,
                ['coin' => $coin]
            );

            $this->assertResponseIsSuccessful();
        }

        // Act - Return all the money
        $client->jsonRequest(
            'POST',
            self::API_ENDPOINT_RETURN,
            []
        );

        // Assert
        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('coins_returned', $responseData);
        $this->assertIsArray($responseData['coins_returned']);
        
        $totalReturned = array_sum($responseData['coins_returned']);
        $this->assertEquals($expectedTotal, $totalReturned, "Total returned should match total inserted");
        $this->assertCount(count($coinsToInsert), $responseData['coins_returned']);
    }

    private function resetMachineState(): void
    {
        $userMoneyFile = __DIR__ . '/../../src/var/user_money.json';
        
        if (file_exists($userMoneyFile)) {
            unlink($userMoneyFile);
        }
    }
}
