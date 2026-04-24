<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderItem;
use Siganushka\OrderBundle\Tests\Fixtures\Subject;

class OrderItemTest extends TestCase
{
    #[DataProvider('validOrderItemProvider')]
    public function testAll(int $price, int $quantity, int $subtotal): void
    {
        $subject = new Subject(id: 1, title: 'foo', price: $price, subtitle: 'bar', img: 'baz');
        $item = new MyOrderItem($subject, $quantity);

        static::assertNull($item->getOrder());
        static::assertSame($subject, $item->getSubject());
        static::assertSame('foo', $item->getTitle());
        static::assertSame('bar', $item->getSubtitle());
        static::assertSame('baz', $item->getImg());
        static::assertSame($price, $item->getPrice());
        static::assertSame($quantity, $item->getQuantity());
        static::assertSame($subtotal, $item->getSubtotal());
    }

    public static function validOrderItemProvider(): iterable
    {
        yield [0, 3, 0];
        yield [10, 1, 10];
        yield [20, 10, 200];
        yield [50, 9, 450];
        yield [100, 12, 1200];
    }
}
