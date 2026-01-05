<?php

namespace App\Tests\Service\Payment;

use App\Service\Payment\PaymentProcessorFactory;
use App\Service\Payment\PaymentProcessorInterface;
use PHPUnit\Framework\TestCase;

class PaymentProcessorFactoryTest extends TestCase
{
    private PaymentProcessorFactory $factory;
    private PaymentProcessorInterface $paypalProcessor;
    private PaymentProcessorInterface $stripeProcessor;

    protected function setUp(): void
    {
        $this->paypalProcessor = new class implements PaymentProcessorInterface {
            public function pay(string $amount): void {}
            public static function getName(): string { return 'paypal'; }
        };

        $this->stripeProcessor = new class implements PaymentProcessorInterface {
            public function pay(string $amount): void {}
            public static function getName(): string { return 'stripe'; }
        };

        $this->factory = new PaymentProcessorFactory([
            $this->paypalProcessor,
            $this->stripeProcessor,
        ]);
    }

    public function testGetAvailableProcessors(): void
    {
        $processors = $this->factory->getAvailableProcessors();
        \sort($processors);
        self::assertEquals(['paypal', 'stripe'], $processors);
    }

    public function testGetProcessorSuccess(): void
    {
        $processor = $this->factory->getProcessor('paypal');
        self::assertSame($this->paypalProcessor, $processor);
    }

    public function testGetProcessorUnknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->getProcessor('unknown');
    }
}
