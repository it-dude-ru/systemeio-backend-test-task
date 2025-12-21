<?php

namespace App\Service\Payment;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalPaymentProcessorAdapter implements PaymentProcessorInterface
{
    private PaypalPaymentProcessor $processor;

    public function __construct()
    {
        $this->processor = new PaypalPaymentProcessor();
    }

    public function pay(string $amount): void
    {
        $amountInCents = (int)\bcmul($amount, '100');
        $this->processor->pay($amountInCents);
    }

    public static function getName(): string
    {
        return 'paypal';
    }
}
