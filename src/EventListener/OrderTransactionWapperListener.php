<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderTransactionWapperListener implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function onOrderBeforeCreate(): void
    {
        $this->entityManager->beginTransaction();
    }

    public function onOrderCreated(): void
    {
        $this->entityManager->commit();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', 16],
            OrderCreatedEvent::class => ['onOrderCreated', -16],
        ];
    }
}
