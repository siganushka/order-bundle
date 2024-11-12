<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Modifier;

use Siganushka\OrderBundle\Entity\Order;

interface OrderInventoryModifierInterface
{
    public const INCREMENT = 1;
    public const DECREMENT = 2;

    /**
     * @throws \InvalidArgumentException Triggered when the subject or quantity is invalid
     * @throws \UnhandledMatchError      Triggered when the action is invalid
     * @throws \RuntimeException         Triggered when an update fails
     */
    public function modifiy(Order $order, int $action): void;
}
