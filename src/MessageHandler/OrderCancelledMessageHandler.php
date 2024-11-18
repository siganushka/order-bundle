<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierinterface;
use Siganushka\OrderBundle\Message\OrderCancelledMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderCancelledMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderStateFlow,
        private readonly OrderInventoryModifierinterface $inventoryModifier)
    {
    }

    public function __invoke(OrderCancelledMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumber($message->getNumber());
        if (!$entity) {
            return;
        }

        $this->entityManager->beginTransaction();

        $this->orderStateFlow->apply($entity, 'cancel');
        $this->inventoryModifier->modifiy(OrderInventoryModifierinterface::INCREASE, $entity);

        $this->entityManager->flush();
        $this->entityManager->commit();
    }
}
