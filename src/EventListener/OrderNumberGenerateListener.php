<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderNumberGenerateListener implements EventSubscriberInterface
{
    public function __construct(protected readonly OrderNumberGeneratorInterface $generator)
    {
    }

    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $order = $event->getOrder();

        $number = $this->generator->generate($order);
        $order->setNumber($number);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', 8],
        ];
    }
}
