<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Doctrine;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderConfirmFreeListener
{
    public function __construct(private readonly WorkflowInterface $orderStateFlow)
    {
    }

    public function prePersist(Order $entity): void
    {
        if ($entity->isFree()) {
            $this->orderStateFlow->apply($entity, OrderStateTransition::Pay->value);
        }
    }
}
