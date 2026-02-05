<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderAdjustment;
use Symfony\Contracts\Translation\TranslatableInterface;

class OrderAdjustmentTest extends TestCase
{
    #[DataProvider('validAmountProvider')]
    public function testAll(int $amount): void
    {
        $adjustment = new MyOrderAdjustment($amount);
        static::assertSame($amount, $adjustment->getAmount());
        static::assertSame('my_order_adjustment', $adjustment->getType());
        static::assertInstanceOf(TranslatableInterface::class, $adjustment->getLabel());
    }

    public function testSetAmountException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('The amount cannot be modified anymore.');

        $adjustment = new MyOrderAdjustment(1);
        $adjustment->setAmount(128);
    }

    public static function validAmountProvider(): iterable
    {
        yield [-1];
        yield [-1024];
        yield [16];
        yield [65535];
        yield [\PHP_INT_MAX];
    }
}
