<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class OrderExpireMessageListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly int $expires)
    {
    }

    public function __invoke(Order $entity): void
    {
        $number = $entity->getNumber();
        if (null === $number || $entity->isFree()) {
            return;
        }

        $message = new OrderExpireMessage($number);
        $envelope = (new Envelope($message))
            ->with(new DelayStamp($this->expires * 1000))
        ;

        $this->messageBus->dispatch($envelope);
    }
}
