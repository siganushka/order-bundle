<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Stock;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Exception\OutOfStockException;

interface OrderStockModifierInterface
{
    /**
     * Increment stock quantity for order.
     *
     * @param Order $order The order object
     */
    public function increment(Order $order): void;

    /**
     * Decrement stock quantity for order.
     *
     * @param Order $order The order object
     *
     * @throws OutOfStockException triggered when out of stock
     */
    public function decrement(Order $order): void;
}
