<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Modifier;

use Siganushka\OrderBundle\Entity\Order;

interface OrderInventoryModifierInterface
{
    public const INCREMENT = 1;
    public const DECREMENT = 2;

    /**
     * @throws \UnhandledMatchError Triggered when the action is invalid
     * @throws \RuntimeException    Triggered when insufficient inventory
     */
    public function modifiy(Order $order, int $action): void;
}
