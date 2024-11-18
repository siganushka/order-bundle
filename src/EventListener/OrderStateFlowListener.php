<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderStateFlowListener implements EventSubscriberInterface
{
    public function onGuardForReset(GuardEvent $event): void
    {
        /** @var Order */
        $subject = $event->getSubject();
        if ($subject->isFree() && OrderState::Confirmed === $subject->getState()) {
            $event->setBlocked(true);
        }
    }

    public function onTransitionForReset(TransitionEvent $event): void
    {
        /** @var Order */
        $subject = $event->getSubject();
        if ($subject->isFree()) {
            $marking = $event->getMarking();
            $marking->mark(OrderState::Confirmed->value);
        }
    }

    /**
     * @see https://symfony.com/doc/current/workflow.html#using-events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GuardEvent::getName('order_state_flow', 'reset') => 'onGuardForReset',
            TransitionEvent::getName('order_state_flow', 'reset') => 'onTransitionForReset',
        ];
    }
}
