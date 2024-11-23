<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsEventListener(event: OrderCreatedEvent::class)]
class OrderExpireMessageListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly int $expires = 1800)
    {
    }

    public function __invoke(OrderCreatedEvent $event): void
    {
        $entity = $event->getOrder();
        if (!$entity->getNumber()) {
            return;
        }

        $message = new OrderExpireMessage($entity->getNumber());
        $envelope = (new Envelope($message))
            ->with(new DelayStamp($this->expires * 1000))
        ;

        $this->messageBus->dispatch($envelope);
    }
}
