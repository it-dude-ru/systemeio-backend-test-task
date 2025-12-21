<?php

namespace App\Controller;

use App\DTO\PurchaseRequest;
use App\Exception\BusinessValidationException;
use App\Service\Payment\PaymentProcessorFactory;
use App\Service\PriceManager;
use App\Service\RequestDtoResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class PurchaseController extends AbstractController
{
    public function __construct(
        private PriceManager $priceManager,
        private PaymentProcessorFactory $paymentProcessorFactory,
    ) {}

    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function index(
        Request $request,
        RequestDtoResolver $resolver
    ): JsonResponse {
        try {
            /** @var PurchaseRequest $dto */
            $dto = $resolver->resolve($request, PurchaseRequest::class);

            $totalPrice = $this->priceManager->calculatePrice($dto);

            $processor = $this->paymentProcessorFactory->getProcessor($dto->paymentProcessor);
            $processor->pay($totalPrice);

            return $this->json([
                'status' => 'success',
                'price' => (float) $totalPrice,
            ]);

            // todo Многовато типов эксепшена
        } catch (BusinessValidationException $e) {
            return $this->json(
                ['errors' => [['field' => $e->getField(), 'message' => $e->getMessage()]]],
                422
            );
        } catch (BadRequestException $e) {
            return new JsonResponse(json_decode($e->getMessage(), true), 422);
        } catch (\Exception $e) {
            return $this->json(['errors' => [['message' => $e->getMessage()]]], 400);
        }
    }
}
