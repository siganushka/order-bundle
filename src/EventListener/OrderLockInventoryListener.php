<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderLockInventoryListener implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $order = $event->getOrder();
        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            if ($subject) {
                $this->entityManager->refresh($subject, LockMode::PESSIMISTIC_WRITE);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', 4],
        ];
    }
}
