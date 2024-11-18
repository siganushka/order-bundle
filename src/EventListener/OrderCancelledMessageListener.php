<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Siganushka\OrderBundle\Message\OrderCancelledMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderCancelledMessageListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly WorkflowInterface $orderStateFlow,
        private readonly int $expiresIn = 60 * 1000)
    {
    }

    public function onOrderCreated(OrderCreatedEvent $event): void
    {
        $order = $event->getOrder();
        if (!$order->getNumber()) {
            return;
        }

        // Check by workflow...
        if (!$this->orderStateFlow->can($order, 'cancel')) {
            return;
        }

        $message = new OrderCancelledMessage($order->getNumber());
        $envelope = (new Envelope($message))
            ->with(new DelayStamp($this->expiresIn))
        ;

        $this->messageBus->dispatch($envelope);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderCreatedEvent::class => 'onOrderCreated',
        ];
    }
}
