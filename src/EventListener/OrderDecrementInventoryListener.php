<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderDecrementInventoryListener implements EventSubscriberInterface
{
    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $order = $event->getOrder();
        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            $subject?->decrementInventory($item->getQuantity() ?? 0);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => 'onOrderBeforeCreate',
        ];
    }
}
