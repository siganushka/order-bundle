<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderAdjustment;
use Symfony\Contracts\Translation\TranslatableInterface;

class OrderAdjustmentTest extends TestCase
{
    /**
     * @dataProvider validAmountProvider
     */
    public function testAll(?int $amount): void
    {
        $adjustment = new MyOrderAdjustment();
        static::assertNull($adjustment->getAmount());
        static::assertSame('my_order_adjustment', $adjustment->getType());
        static::assertInstanceOf(TranslatableInterface::class, $adjustment->getLabel());

        $adjustment->setAmount($amount);
        static::assertSame($amount, $adjustment->getAmount());
    }

    /**
     * @return array<int, array<?int>>
     */
    public function validAmountProvider(): array
    {
        return [
            [-1],
            [-1024],
            [16],
            [65535],
            [\PHP_INT_MAX],
        ];
    }
}
