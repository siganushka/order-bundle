<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderLockInventoryListener implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $this->entityManager->beginTransaction();

        $order = $event->getOrder();
        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            if ($subject) {
                $this->entityManager->refresh($subject, LockMode::PESSIMISTIC_WRITE);
            }
        }
    }

    public function onOrderCreated(OrderCreatedEvent $event): void
    {
        $this->entityManager->commit();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', 4],
            OrderCreatedEvent::class => ['onOrderCreated', -128],
        ];
    }
}
