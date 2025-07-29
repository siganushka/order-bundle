<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Enum\OrderStateFlow;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderCheckFreeListener
{
    public function __construct(private readonly WorkflowInterface $orderStateFlow)
    {
    }

    public function __invoke(Order $entity): void
    {
        if ($entity->isFree() && OrderState::Pending === $entity->getState()) {
            $this->orderStateFlow->apply($entity, OrderStateFlow::Pay->value);
        }
    }
}
