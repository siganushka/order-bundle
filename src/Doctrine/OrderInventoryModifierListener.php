<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;

#[AsEntityListener(event: Events::prePersist, entity: Order::class)]
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
