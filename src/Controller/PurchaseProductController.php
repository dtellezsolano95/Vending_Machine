<?php

namespace App\Controller;

use App\Application\Product\Purchase\PurchaseProductRequest;
use App\Application\Product\Purchase\PurchaseProductUseCase;
use App\Domain\Money\Exception\InvalidCoinValueException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/product')]
class PurchaseProductController extends AbstractController
{
    private PurchaseProductUseCase $purchaseProductUseCase;

    public function __construct(PurchaseProductUseCase $purchaseProductUseCase)
    {
        $this->purchaseProductUseCase = $purchaseProductUseCase;
    }

    /**
    * Purchase product from vending machine
    */
    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $purchaseRequest = new PurchaseProductRequest($data['product'] ?? null);
            
            $response = $this->purchaseProductUseCase->execute($purchaseRequest);

            return $this->json([
                'product_name' => $response->productName(),
                'change_returned' => $response->changeReturned()
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
