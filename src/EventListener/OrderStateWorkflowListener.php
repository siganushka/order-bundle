<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\AnnounceEvent;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\EnteredEvent;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\LeaveEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderStateWorkflowListener implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onGuard(GuardEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function onLeave(LeaveEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function onTransition(TransitionEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function onEnter(EnterEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function onEntered(EnteredEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function onCompleted(CompletedEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function onAnnounce(AnnounceEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    /**
     * @see https://symfony.com/doc/current/workflow.html#using-events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GuardEvent::getName('order_state', null) => 'onGuard',
            LeaveEvent::getName('order_state', null) => 'onLeave',
            TransitionEvent::getName('order_state', null) => 'onTransition',
            EnterEvent::getName('order_state', null) => 'onEnter',
            EnteredEvent::getName('order_state', null) => 'onEntered',
            CompletedEvent::getName('order_state', null) => 'onCompleted',
            AnnounceEvent::getName('order_state', null) => 'onAnnounce',
        ];
    }
}
