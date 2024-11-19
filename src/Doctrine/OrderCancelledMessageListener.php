<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Doctrine;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Message\OrderCancelledMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class OrderCancelledMessageListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly int $expireIn = 1800)
    {
    }

    public function preFlush(Order $entity): void
    {
        if (!$entity->getNumber()) {
            return;
        }

        $message = new OrderCancelledMessage($entity->getNumber());
        $envelope = (new Envelope($message))
            ->with(new DelayStamp($this->expireIn * 1000))
        ;

        $this->messageBus->dispatch($envelope);
    }
}
