<?php

namespace App\Controller;

use App\Application\Service\ServiceRequest;
use App\Application\Service\ServiceUseCase;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/service')]
class ServiceController extends AbstractController
{
    private ServiceUseCase $serviceUseCase;

    public function __construct(ServiceUseCase $serviceUseCase)
    {
        $this->serviceUseCase = $serviceUseCase;
    }

    /**
    * Service endpoint for technicians to set product stock and change coins
    */
    #[Route('', name: 'service', methods: ['POST'])]
    public function service(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $serviceRequest = new ServiceRequest($data);
            
            $response = $this->serviceUseCase->execute($serviceRequest);

            return $this->json([
                'items_updated' => $response->itemsUpdated(),
                'change_updated' => $response->changeUpdated()
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'An error occurred while processing the service request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
