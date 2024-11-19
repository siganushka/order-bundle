<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Message\OrderCancelledMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderCancelledMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderStateFlow)
    {
    }

    public function __invoke(OrderCancelledMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumber($message->getNumber());
        if (!$entity) {
            return;
        }

        if (!$this->orderStateFlow->can($entity, OrderStateTransition::Cancel->value)) {
            return;
        }

        $this->entityManager->beginTransaction();

        $this->orderStateFlow->apply($entity, OrderStateTransition::Cancel->value);

        $this->entityManager->flush();
        $this->entityManager->commit();
    }
}
