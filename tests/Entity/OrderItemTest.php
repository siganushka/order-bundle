<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderItem;
use Siganushka\OrderBundle\Tests\Fixtures\QuantityAwareSubject;
use Siganushka\OrderBundle\Tests\Fixtures\Subject;

class OrderItemTest extends TestCase
{
    #[DataProvider('validOrderItemProvider')]
    public function testAll(int $price, int $quantity, int $subtotal): void
    {
        $subject = new Subject(1, 'foo', $price, 'bar', 'baz');
        $item = new MyOrderItem($subject, $quantity);

        static::assertInstanceOf(Subject::class, $item->getSubject());
        static::assertSame($price, $item->getPrice());
        static::assertSame($quantity, $item->getQuantity());
        static::assertSame($subtotal, $item->getSubtotal());
        static::assertSame(1, $item->getSubjectId());
        static::assertSame('foo', $item->getSubjectTitle());
        static::assertSame('bar', $item->getSubjectSubtitle());
        static::assertSame('baz', $item->getSubjectImg());
    }

    public function testQuantityAwareSubject(): void
    {
        $subject = new QuantityAwareSubject(1, 'foo', 100);
        $item1 = new MyOrderItem($subject, 1);
        $item2 = new MyOrderItem($subject, 100);
        $item3 = new MyOrderItem($subject, 1000);
        $item4 = new MyOrderItem($subject, 10000);

        static::assertSame(100, $item1->getPrice());
        static::assertSame(90, $item2->getPrice());
        static::assertSame(80, $item3->getPrice());
        static::assertSame(50, $item4->getPrice());
    }

    public function testSetSubjectException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('The subject cannot be modified anymore.');

        $item = new MyOrderItem(new Subject(1, 'foo', 100), 1);
        $item->setSubject(new Subject(2, 'bar', 200));
    }

    public function testSetPriceException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('The price cannot be modified anymore.');

        $item = new MyOrderItem(new Subject(1, 'foo', 100), 1);
        $item->setPrice(128);
    }

    public function testSetQuantityException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('The quantity cannot be modified anymore.');

        $item = new MyOrderItem(new Subject(1, 'foo', 100), 1);
        $item->setQuantity(128);
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
