<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Inventory;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Exception\InsufficientInventoryException;

interface OrderInventoryModifierInterface
{
    public const INCREASE = 1;
    public const DECREASE = 2;

    /**
     * Increase/Decrease inventory quantity for order.
     *
     * @param int   $action The action type: 1 Increase/ 2 Decrease
     * @param Order $order  The order object
     *
     * @throws \UnhandledMatchError           The argument "action" is invalid
     * @throws InsufficientInventoryException Triggered when insufficient inventory
     */
    public function modifiy(int $action, Order $order): void;
}
