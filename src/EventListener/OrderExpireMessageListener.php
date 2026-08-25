<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class OrderExpireMessageListener
{
    /**
     * @var array<int, AbstractOrder>
     */
    private array $pendingOrders = [];

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        #[Autowire(param: 'siganushka_order.order_expire_seconds')]
        private readonly int $seconds)
    {
    }

    public function __invoke(AbstractOrder $entity): void
    {
        $this->pendingOrders[] = $entity;
    }

    public function postFlush(): void
    {
        $orders = $this->pendingOrders;
        $this->pendingOrders = [];

        foreach ($orders as $entity) {
            $number = $entity->getNumber();
            if (null === $number || OrderState::Pending !== $entity->getState()) {
                continue;
            }

            $message = new OrderExpireMessage($number);
            $envelope = (new Envelope($message))
                ->with(new DelayStamp($this->seconds * 1000))
            ;

            $this->messageBus->dispatch($envelope);
        }
    }
}
