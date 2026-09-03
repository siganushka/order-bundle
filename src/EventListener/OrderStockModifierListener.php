<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Stock\OrderStockModifierInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\EnteredEvent;

class OrderStockModifierListener implements EventSubscriberInterface
{
    public function __construct(private readonly OrderStockModifierInterface $stockModifier)
    {
    }

    public function __invoke(AbstractOrder $entity): void
    {
        $this->stockModifier->decrement($entity);
    }

    /**
     * @param EnteredEvent<AbstractOrder> $event
     */
    public function increment(EnteredEvent $event): void
    {
        $this->stockModifier->increment($event->getSubject());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EnteredEvent::getName('order', OrderState::Cancelled->value) => 'increment',
        ];
    }
}
