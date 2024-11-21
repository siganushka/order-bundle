<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsEventListener(event: OrderBeforeCreateEvent::class, priority: -8)]
class OrderCheckFreeListener
{
    public function __construct(private readonly WorkflowInterface $orderStateFlow)
    {
    }

    public function __invoke(OrderBeforeCreateEvent $event): void
    {
        $entity = $event->getOrder();
        if ($entity->isFree()) {
            $this->orderStateFlow->apply($entity, OrderStateTransition::Pay->value);
        }
    }
}
