<?php

namespace App\Service;

use App\DTO\CalculatePriceRequest;
use App\Exception\BusinessValidationException;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;

class PriceManager
{
    public function __construct(
        private ProductRepository $productRepository,
        private CouponRepository $couponRepository,
        private PriceCalculator $priceCalculator,
    ) {}

    /**
     * @throws BusinessValidationException
     */
    public function calculatePrice(CalculatePriceRequest $dto): string
    {
        $product = $this->productRepository->find($dto->product);

        if (null === $product) {
            throw new BusinessValidationException('product', 'Product not found');
        }

        $coupon = null;
        if (null !== $dto->couponCode) {
            $coupon = $this->couponRepository->findOneBy(['code' => $dto->couponCode]);

            if (null === $coupon) {
                throw new BusinessValidationException('couponCode', 'Invalid coupon');
            }
        }

        return $this->priceCalculator->calculate(
            $product,
            $dto->taxNumber,
            $coupon
        );
    }
}
