<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Message\OrderCancelMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class OrderCancelMessageListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly int $seconds)
    {
    }

    public function __invoke(Order $entity): void
    {
        $number = $entity->getNumber();
        if (null === $number || OrderState::Pending !== $entity->getState()) {
            return;
        }

        $message = new OrderCancelMessage($number);
        $envelope = (new Envelope($message))
            ->with(new DelayStamp($this->seconds * 1000))
        ;

        $this->messageBus->dispatch($envelope);
    }
}
