<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class OrderExpireMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderStateMachine)
    {
    }

    public function __invoke(OrderExpireMessage $message): void
    {
        try {
            $this->entityManager->wrapInTransaction(fn () => $this->handle($message));
        } catch (\Throwable $th) {
            $this->logger->error('Order expire error.', ['msg' => $th->getMessage()]);
        }
    }

    private function handle(OrderExpireMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumberWithLock($message->getNumber())
            ?? throw new UnrecoverableMessageHandlingException('Order not found.');

        if ($this->orderStateMachine->can($entity, $transitionName = OrderStateTransition::Expire->value)) {
            $this->orderStateMachine->apply($entity, $transitionName);
        }
    }
}
