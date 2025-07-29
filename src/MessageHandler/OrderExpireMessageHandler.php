<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Enum\OrderStateFlow;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderExpireMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderStateFlow)
    {
    }

    public function __invoke(OrderExpireMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumber($message->getNumber());
        if (!$entity) {
            return;
        }

        // Target transition name as string.
        $transitionName = OrderStateFlow::Expire->value;
        if (!$this->orderStateFlow->can($entity, $transitionName)) {
            return;
        }

        $this->entityManager->beginTransaction();

        $this->orderStateFlow->apply($entity, $transitionName);

        $this->entityManager->flush();
        $this->entityManager->commit();
    }
}
