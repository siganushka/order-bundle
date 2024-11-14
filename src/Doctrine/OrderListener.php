<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Modifier\OrderInventoryModifierInterface;

#[AsEntityListener(event: Events::prePersist, entity: Order::class)]
class OrderListener
{
    public function __construct(
        private readonly OrderNumberGeneratorInterface $generator,
        private readonly OrderInventoryModifierInterface $inventoryModifier)
    {
    }

    public function prePersist(Order $entity): void
    {
        if (!$entity->getNumber()) {
            $entity->setNumber($this->generator->generate($entity));
        }

        $this->inventoryModifier->modifiy($entity, OrderInventoryModifierInterface::DECREMENT);
    }
}
