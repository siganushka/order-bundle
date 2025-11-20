<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderExpireMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderStateMachine,
    ) {
    }

    public function __invoke(OrderExpireMessage $message): void
    {
        $this->entityManager->beginTransaction();

        try {
            $queryBuilder = $this->orderRepository->createQueryBuilder('o')
                ->where('o.number = :number')
                ->setParameter('number', $message->getNumber())
                ->setMaxResults(1)
            ;

            // [important] Using Pessimistic Locking.
            $query = $queryBuilder->getQuery();
            $query->setLockMode(LockMode::PESSIMISTIC_WRITE);

            $entity = $query->getOneOrNullResult();
            if (!$entity instanceof Order) {
                throw new UnrecoverableMessageHandlingException('Order not found.');
            }

            try {
                $this->orderStateMachine->apply($entity, OrderStateTransition::Expire->value);
            } catch (\Throwable $th) {
                throw new UnrecoverableMessageHandlingException($th->getMessage());
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $connection = $this->entityManager->getConnection();
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            if ($exception instanceof UnrecoverableMessageHandlingException) {
                return;
            }

            throw $exception;
        }
    }
}
