<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierinterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderInventoryModifierListener implements EventSubscriberInterface
{
    public function __construct(private readonly OrderInventoryModifierinterface $inventoryModifier)
    {
    }

    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $entity = $event->getOrder();

        // Decrease inventory
        $this->inventoryModifier->modifiy(OrderInventoryModifierinterface::DECREASE, $entity);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', -16],
        ];
    }
}
