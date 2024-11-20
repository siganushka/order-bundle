<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: OrderBeforeCreateEvent::class, priority: 8)]
class OrderNumberGenerateListener
{
    public function __construct(private readonly OrderNumberGeneratorInterface $numberGenerator)
    {
    }

    public function __invoke(OrderBeforeCreateEvent $event): void
    {
        $entity = $event->getOrder();
        if (!$entity->getNumber()) {
            $entity->setNumber($this->numberGenerator->generate($entity));
        }
    }
}
