<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Stock;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Exception\InsufficientStockException;

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
     * @throws InsufficientStockException Triggered when insufficient stock
     */
    public function decrement(Order $order): void;
}
