<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderNumberGenerateListener implements EventSubscriberInterface
{
    public function __construct(private readonly OrderNumberGeneratorInterface $generator)
    {
    }

    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $entity = $event->getOrder();
        if (!$entity->getNumber()) {
            $entity->setNumber($this->generator->generate($entity));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => ['onOrderBeforeCreate', 8],
        ];
    }
}
