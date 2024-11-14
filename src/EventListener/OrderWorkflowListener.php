<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Modifier\OrderInventoryModifierInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderWorkflowListener implements EventSubscriberInterface
{
    public function __construct(private readonly OrderInventoryModifierInterface $inventoryModifier)
    {
    }

    public function onTransition(TransitionEvent $event): void
    {
        $transition = $event->getTransition();
        if (!$transition || !\in_array($transition->getName(), ['refund', 'cancel'], true)) {
            return;
        }

        /** @var Order */
        $order = $event->getSubject();
        $this->inventoryModifier->modifiy($order, OrderInventoryModifierInterface::INCREMENT);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName('order_state', null) => 'onTransition',
        ];
    }
}
