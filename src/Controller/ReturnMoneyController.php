<?php

namespace App\Controller;

use App\Application\Money\Return\ReturnMoneyUseCase;
use App\Domain\Money\Exception\InvalidCoinValueException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/money')]
class ReturnMoneyController extends AbstractController
{
    private ReturnMoneyUseCase $returnMoneyUseCase;

    public function __construct(ReturnMoneyUseCase $returnMoneyUseCase)
    {
        $this->returnMoneyUseCase = $returnMoneyUseCase;
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
