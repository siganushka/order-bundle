<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Stock\OrderStockModifierInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderStockModifierListener implements EventSubscriberInterface
{
    public function __construct(private readonly OrderStockModifierInterface $stockModifier)
    {
    }

    public function __invoke(Order $entity): void
    {
        $this->stockModifier->decrement($entity);
    }

    public function increment(TransitionEvent $event): void
    {
        $subject = $event->getSubject();
        if ($subject instanceof Order) {
            $this->stockModifier->increment($subject);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName('order', OrderStateTransition::Cancel->value) => 'increment',
            TransitionEvent::getName('order', OrderStateTransition::Expire->value) => 'increment',
        ];
    }
}
