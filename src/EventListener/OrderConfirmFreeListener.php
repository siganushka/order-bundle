<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderConfirmFreeListener implements EventSubscriberInterface
{
    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $order = $event->getOrder();
        if ($order->isFree()) {
            $order->setState(OrderState::Confirmed);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Must be placed after all adjustments
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', -4],
        ];
    }
}
