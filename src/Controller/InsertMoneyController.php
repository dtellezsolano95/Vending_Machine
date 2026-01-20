<?php

namespace App\Controller;

use App\Application\Money\Insert\InsertMoneyRequest;
use App\Application\Money\Insert\InsertMoneyUseCase;
use App\Domain\Money\Exception\InvalidCoinValueException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/money', name: 'api_money_')]
class InsertMoneyController extends AbstractController
{
    private InsertMoneyUseCase $insertMoneyUseCase;

    public function __construct(InsertMoneyUseCase $insertMoneyUseCase)
    {
        $this->insertMoneyUseCase = $insertMoneyUseCase;
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
}
