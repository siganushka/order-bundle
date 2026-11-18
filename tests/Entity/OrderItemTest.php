<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Model\OrderItemSubjectSnapshot;
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

    public function testEmpty(): void
    {
        $item = new MyOrderItem();
        static::assertNull($item->getSubject());
        static::assertNull($item->getTitle());
        static::assertNull($item->getSubtitle());
        static::assertNull($item->getImg());
        static::assertNull($item->getPrice());
        static::assertNull($item->getQuantity());

        $subject = new Subject(id: 1, title: 'foo', price: 123, subtitle: 'bar', img: 'baz');
        $item->setSubject($subject);
        $item->setQuantity(456);

        static::assertSame($subject, $item->getSubject());
        static::assertSame('foo', $item->getTitle());
        static::assertSame('bar', $item->getSubtitle());
        static::assertSame('baz', $item->getImg());
        static::assertSame(123, $item->getPrice());
        static::assertSame(456, $item->getQuantity());
    }

    public function testSnapshotWithoutSubject(): void
    {
        $snapshot = new OrderItemSubjectSnapshot(
            title: 'foo',
            subtitle: '定制（4XL 加大码）',
            price: 12900,
            img: 'baz',
        );

        $item = new MyOrderItem();
        $item->setSnapshot($snapshot);
        $item->setQuantity(10);

        static::assertNull($item->getSubject());
        static::assertSame('foo', $item->getTitle());
        static::assertSame('定制（4XL 加大码）', $item->getSubtitle());
        static::assertSame('baz', $item->getImg());
        static::assertSame(12900, $item->getPrice());
        static::assertSame(10, $item->getQuantity());
        static::assertSame(129000, $item->getSubtotal());
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
