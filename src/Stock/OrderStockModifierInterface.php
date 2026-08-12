<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Stock;

use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Exception\OutOfStockException;

interface OrderStockModifierInterface
{
    /**
     * Increment stock quantity for order.
     */
    public function increment(AbstractOrder $order): void;

    /**
     * Decrement stock quantity for order.
     *
     * @throws OutOfStockException triggered when out of stock
     */
    public function decrement(AbstractOrder $order): void;
}
