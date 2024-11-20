<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Message\OrderCancelMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderCancelMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderStateFlow)
    {
    }

    public function __invoke(OrderCancelMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumber($message->getNumber());
        if (!$entity) {
            return;
        }

        // Target transition name as string.
        $transitionName = OrderStateTransition::Cancel->value;
        if (!$this->orderStateFlow->can($entity, $transitionName)) {
            return;
        }

        $this->entityManager->beginTransaction();

        $this->orderStateFlow->apply($entity, $transitionName);

        $this->entityManager->flush();
        $this->entityManager->commit();
    }
}
