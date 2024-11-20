<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Siganushka\OrderBundle\Message\OrderCancelMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsEventListener(event: OrderCreatedEvent::class)]
class OrderCancelMessageListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly int $expireIn = 1800)
    {
    }

    public function __invoke(OrderCreatedEvent $event): void
    {
        $entity = $event->getOrder();
        if (!$entity->getNumber()) {
            return;
        }

        $message = new OrderCancelMessage($entity->getNumber());
        $envelope = (new Envelope($message))
            ->with(new DelayStamp($this->expireIn * 1000))
        ;

        $this->messageBus->dispatch($envelope);
    }
}
