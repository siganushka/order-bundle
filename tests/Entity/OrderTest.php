<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrder;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderAdjustment;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderItem;
use Siganushka\OrderBundle\Tests\Fixtures\Subject;

class OrderTest extends TestCase
{
    #[DataProvider('orderDataProvider')]
    public function testAll(array $prices, array $adjustments, int $total): void
    {
        $order = new MyOrder();
        static::assertNull($order->getNumber());
        static::assertSame(0, $order->getItemsTotal());
        static::assertSame(0, $order->getAdjustmentsTotal());
        static::assertSame(0, $order->getTotal());
        static::assertNull($order->getNote());
        static::assertSame(OrderState::Pending, $order->getState());
        static::assertCount(0, $order->getItems());
        static::assertCount(0, $order->getAdjustments());

        $order->setNumber('foo');
        $order->setNote('test note');
        $order->setState(OrderState::Completed);

        static::assertSame('foo', $order->getNumber());
        static::assertSame('test note', $order->getNote());
        static::assertSame(OrderState::Completed, $order->getState());

        array_walk($prices, static fn (int $price) => $order->addItem(new MyOrderItem(new Subject(1, 'foo', $price), 1)));
        array_walk($adjustments, static fn (int $amount) => $order->addAdjustment(new MyOrderAdjustment($amount)));

        $itemsTotal = array_sum($prices);
        $adjustmentsTotal = array_sum($adjustments);

        static::assertSame($itemsTotal, $order->getItemsTotal());
        static::assertSame($adjustmentsTotal, $order->getAdjustmentsTotal());
        static::assertSame($total, $order->getTotal());
        static::assertCount(\count($prices), $order->getItems());
        static::assertCount(\count($adjustments), $order->getAdjustments());

        $firstItem = $order->getItems()->first();
        static::assertInstanceOf(MyOrderItem::class, $firstItem);

        $firstItemSubject = $firstItem->getSubject();
        static::assertInstanceOf(Subject::class, $firstItemSubject);

        $firstAdjustment = $order->getAdjustments()->first();
        static::assertInstanceOf(MyOrderAdjustment::class, $firstAdjustment);

        $order->clearItems();
        static::assertSame(0, $order->getItemsTotal());
        static::assertSame($adjustmentsTotal, $order->getAdjustmentsTotal());
        static::assertSame($adjustmentsTotal > 0 ? $adjustmentsTotal : 0, $order->getTotal());
        static::assertCount(0, $order->getItems());
        static::assertCount(\count($adjustments), $order->getAdjustments());

        $order->clearAdjustments();
        static::assertSame(0, $order->getItemsTotal());
        static::assertSame(0, $order->getAdjustmentsTotal());
        static::assertSame(0, $order->getTotal());
        static::assertCount(0, $order->getItems());
        static::assertCount(0, $order->getAdjustments());
    }

    public static function orderDataProvider(): iterable
    {
        yield [[0], [0], 0];
        yield [[7, 0], [6], 13];
        yield [[10, 11, 11], [-4, 6], 34];
        yield [[20, 15], [-6, 1], 30];
        yield [[50, 100, 350, 0], [12, 10, -6], 516];
        yield [[100, 1, 12], [-2, 0], 111];
        yield [[30, 15], [-15, -50], 0];
    }
}
