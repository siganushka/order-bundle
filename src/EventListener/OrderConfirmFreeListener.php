<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Registry;

class OrderConfirmFreeListener implements EventSubscriberInterface
{
    public function __construct(private readonly Registry $registry)
    {
    }

    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $entity = $event->getOrder();
        if (!$entity->isFree()) {
            $this->registry->get($entity)->apply($entity, 'pay');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', -4],
        ];
    }
}
