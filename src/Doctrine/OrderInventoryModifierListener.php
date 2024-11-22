<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Doctrine;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;

class OrderInventoryModifierListener
{
    public function __construct(private readonly OrderInventoryModifierInterface $inventoryModifier)
    {
    }

    public function prePersist(Order $entity): void
    {
        $this->inventoryModifier->modifiy(OrderInventoryModifierInterface::DECREASE, $entity);
    }
}
