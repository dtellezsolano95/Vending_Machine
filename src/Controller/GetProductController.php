<?php

namespace App\Controller;

use App\Application\Product\Get\GetProductUseCase;
use App\Domain\Money\Exception\InvalidCoinValueException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/product', name: 'api_product_')]
class GetProductController extends AbstractController
{
    private GetProductUseCase $getProductUseCase;

    public function __construct(GetProductUseCase $getProductUseCase)
    {
        $this->getProductUseCase = $getProductUseCase;
    }

    /**
    * Get specific product from vending machine
    */
    #[Route('/{product}', name: 'product', methods: ['GET'])]
    public function return(Request $request, string $product): JsonResponse
    {
        try {
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
