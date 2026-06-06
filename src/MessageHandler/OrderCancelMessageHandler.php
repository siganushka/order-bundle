<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Message\OrderCancelMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class OrderCancelMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderStateMachine,
    ) {
    }

    public function __invoke(OrderCancelMessage $message): void
    {
        try {
            $this->entityManager->wrapInTransaction(fn () => $this->handle($message));
        } catch (UnrecoverableMessageHandlingException $th) {
            $this->logger->error('Order cancel error.', ['msg' => $th->getMessage()]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function handle(OrderCancelMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumberWithLock($message->getNumber())
            ?? throw new UnrecoverableMessageHandlingException('Order not found.');

        try {
            $this->orderStateMachine->apply($entity, OrderStateTransition::Expire->value);
        } catch (\Throwable $th) {
            throw new UnrecoverableMessageHandlingException($th->getMessage());
        }
    }
}
