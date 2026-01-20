<?php

namespace App\Controller;

use App\Application\Product\Get\GetProductUseCase;
use App\Domain\Money\Exception\InvalidCoinValueException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/product')]
class PurchaseProductController extends AbstractController
{
    private GetProductUseCase $getProductUseCase;

    public function __construct(GetProductUseCase $getProductUseCase)
    {
        $this->getProductUseCase = $getProductUseCase;
    }

    /**
    * Purchase product from vending machine
    */
    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['product'])) {
                return $this->json([
                    'message' => 'Product field is required'
                ], 400);
            }
            
            $product = $data['product'];
            $response = $this->getProductUseCase->execute($product);

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
