<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

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
            $quantity = $item->getQuantity();
            if (null === $subject || null === $quantity) {
                return;
            }

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->update($subject::class, 't');
            $queryBuilder->set('t.inventory', 't.inventory - :quantity');
            $queryBuilder->where('t.id = :id');
            $queryBuilder->andWhere('t.inventory >= :quantity');
            $queryBuilder->setParameter('id', $subject->getId());
            $queryBuilder->setParameter('quantity', $quantity);

            $query = $queryBuilder->getQuery();
            if (!$query->execute()) {
                throw new \RuntimeException('Unable to lock inventory.');
            }
        }
    }

    public function onOrderCreated(): void
    {
        $this->entityManager->commit();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', -8],
            OrderCreatedEvent::class => ['onOrderCreated', -128],
        ];
    }
}
