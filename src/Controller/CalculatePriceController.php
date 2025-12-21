<?php

namespace App\Controller;

use App\DTO\CalculatePriceRequest;
use App\Exception\BusinessValidationException;
use App\Service\PriceManager;
use App\Service\RequestDtoResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CalculatePriceController extends AbstractController
{
    public function __construct(
        private PriceManager $priceManager
    ) {}

    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function index(
        Request $request,
        RequestDtoResolver $resolver
    ): JsonResponse {
        try {
            /** @var CalculatePriceRequest $dto */
            $dto = $resolver->resolve($request, CalculatePriceRequest::class);
            
            $price = $this->priceManager->calculatePrice($dto);
            
            return $this->json(['price' => (float)$price]);

        } catch (BusinessValidationException $e) {
            return $this->json(
                ['errors' => [['field' => $e->getField(), 'message' => $e->getMessage()]]],
                422
            );
        } catch (BadRequestException $e) {
            return new JsonResponse(json_decode($e->getMessage(), true), 422);
        }
    }
}
