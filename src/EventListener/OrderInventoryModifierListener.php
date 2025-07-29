<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderStateFlow;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderInventoryModifierListener implements EventSubscriberInterface
{
    public function __construct(private readonly OrderInventoryModifierInterface $inventoryModifier)
    {
    }

    public function __invoke(Order $entity): void
    {
        $this->inventoryModifier->modifiy(OrderInventoryModifierInterface::DECREASE, $entity);
    }

    public function increase(TransitionEvent $event): void
    {
        $subject = $event->getSubject();
        if ($subject instanceof Order) {
            $this->inventoryModifier->modifiy(OrderInventoryModifierInterface::INCREASE, $subject);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName('order_state_flow', OrderStateFlow::Cancel->value) => 'increase',
            TransitionEvent::getName('order_state_flow', OrderStateFlow::Expire->value) => 'increase',
        ];
    }
}
