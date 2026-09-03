<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderCheckFreeListener
{
    public function __construct(#[Target('order')] private readonly WorkflowInterface $workflow)
    {
    }

    public function __invoke(AbstractOrder $entity): void
    {
        if (OrderState::Pending === $entity->getState() && $entity->getTotal() <= 0) {
            $this->workflow->apply($entity, OrderStateTransition::Confirm->value);
        }
    }
}
