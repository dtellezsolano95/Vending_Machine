<?php

namespace App\Controller;

use App\Application\Money\Insert\InsertMoneyRequest;
use App\Application\Money\Insert\InsertMoneyUseCase;
use App\Application\Money\Return\ReturnMoneyUseCase;
use App\Domain\Money\Exception\InvalidCoinValueException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/money', name: 'api_money_')]
class MoneyController extends AbstractController
{
    private InsertMoneyUseCase $insertMoneyUseCase;
    private ReturnMoneyUseCase $returnMoneyUseCase;

    public function __construct(InsertMoneyUseCase $insertMoneyUseCase, ReturnMoneyUseCase $returnMoneyUseCase)
    {
        $this->insertMoneyUseCase = $insertMoneyUseCase;
        $this->returnMoneyUseCase = $returnMoneyUseCase;
    }

    /**
    * Insert money into the vending machine
    */
    #[Route('/insert', name: 'insert', methods: ['POST'])]
    public function insert(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $insertMoneyRequest = new InsertMoneyRequest($data);
            
            $response = $this->insertMoneyUseCase->execute($insertMoneyRequest);

            return $this->json([
                'coin_inserted' => $response->coinInserted(),
                'current_balance' => round($response->currentBalance(), 2)
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (InvalidCoinValueException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Failed to insert coin'
            ], 500);
        }
    }

    /**
    * Return all inserted money from the vending machine
    */
    #[Route('/return', name: 'return', methods: ['POST'])]
    public function return(Request $request): JsonResponse
    {
        try {
            $response = $this->returnMoneyUseCase->execute();

            return $this->json([
                'coins_returned' => $response->coinsReturned()
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (InvalidCoinValueException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Failed to return coins'
            ], 500);
        }
    }
}
