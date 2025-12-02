<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderItem;
use Siganushka\OrderBundle\Tests\Fixtures\Subject;

class OrderItemTest extends TestCase
{
    /**
     * @dataProvider validOrderItemProvider
     */
    public function testAll(int $price, int $quantity, int $subtotal): void
    {
        $item = new MyOrderItem();
        static::assertNull($item->getSubject());
        static::assertNull($item->getPrice());
        static::assertNull($item->getQuantity());
        static::assertNull($item->getSubtotal());
        static::assertNull($item->getSubjectId());
        static::assertNull($item->getSubjectTitle());
        static::assertNull($item->getSubjectExtra());
        static::assertNull($item->getSubjectImg());

        $item->setSubject(new Subject(1, 'foo', $price, 'bar', 'baz'));
        $item->setQuantity($quantity);

        $subject = $item->getSubject();
        static::assertInstanceOf(Subject::class, $subject);

        static::assertSame($price, $item->getPrice());
        static::assertSame($quantity, $item->getQuantity());
        static::assertSame($subtotal, $item->getSubtotal());
        static::assertSame(1, $item->getSubjectId());
        static::assertSame('foo', $item->getSubjectTitle());
        static::assertSame('bar', $item->getSubjectExtra());
        static::assertSame('baz', $item->getSubjectImg());
    }

    public function testSetPriceException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('The price cannot be modified anymore.');

        $item = new MyOrderItem();
        $item->setPrice(1);
    }

    /**
     * @return array<int, array<?int>>
     */
    public function validOrderItemProvider(): array
    {
        return [
            [0, 3, 0],
            [10, 1, 10],
            [20, 10, 200],
            [50, 9, 450],
            [100, 12, 1200],
        ];
    }
}
