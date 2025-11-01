<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrder;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderAdjustment;
use Siganushka\OrderBundle\Tests\Fixtures\MyOrderItem;
use Siganushka\OrderBundle\Tests\Fixtures\Subject;

class OrderTest extends TestCase
{
    /**
     * @dataProvider orderDataProvider
     *
     * @param array<int> $prices
     * @param array<int> $adjustments
     */
    public function testAll(array $prices, array $adjustments, int $total): void
    {
        $order = new MyOrder();
        static::assertNull($order->getNumber());
        static::assertSame(0, $order->getItemsTotal());
        static::assertSame(0, $order->getAdjustmentsTotal());
        static::assertSame(0, $order->getTotal());
        static::assertSame(OrderState::Pending, $order->getState());
        static::assertSame(OrderState::Pending->value, $order->getStateAsString());
        static::assertNull($order->getNote());
        static::assertCount(0, $order->getItems());
        static::assertCount(0, $order->getAdjustments());

        $order->setNumber('foo');
        $order->setStateAsString(OrderState::Completed->value);
        $order->setNote('test note');

        static::assertSame('foo', $order->getNumber());
        static::assertSame(OrderState::Completed, $order->getState());
        static::assertSame(OrderState::Completed->value, $order->getStateAsString());
        static::assertSame('test note', $order->getNote());

        array_walk($prices, fn (int $price) => $order->addItem(new MyOrderItem(new Subject(1, 'foo', $price), 1)));
        array_walk($adjustments, fn (int $amount) => $order->addAdjustment(new MyOrderAdjustment($amount)));

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

    /**
     * @return array<int, array{ 0: array<int>, 1: array<int>, 2: int }>
     */
    public function orderDataProvider(): array
    {
        return [
            [[0], [0], 0],
            [[7, 0], [6], 13],
            [[10, 11, 11], [-4, 6], 34],
            [[20, 15], [-6, 1], 30],
            [[50, 100, 350, 0], [12, 10, -6], 516],
            [[100, 1, 12], [-2, 0], 111],
        ];
    }
}
