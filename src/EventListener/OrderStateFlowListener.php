<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderStateFlowListener implements EventSubscriberInterface
{
    public function __construct(private readonly OrderInventoryModifierInterface $inventoryModifier)
    {
    }

    public function onTransition(TransitionEvent $event): void
    {
        /** @var Order */
        $subject = $event->getSubject();
        $this->inventoryModifier->modifiy(OrderInventoryModifierInterface::INCREASE, $subject);
    }

    /**
     * @see https://symfony.com/doc/current/workflow.html#using-events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName('order_state_flow', OrderStateTransition::Refund->value) => 'onTransition',
            TransitionEvent::getName('order_state_flow', OrderStateTransition::Cancel->value) => 'onTransition',
        ];
    }
}
