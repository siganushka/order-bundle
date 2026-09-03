<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\DependencyInjection\Attribute\Target;
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
        #[Target('order')]
        private readonly WorkflowInterface $workflow)
    {
    }

    public function __invoke(OrderExpireMessage $message): void
    {
        try {
            $this->entityManager->wrapInTransaction(fn () => $this->handle($message));
        } catch (\Throwable $th) {
            $this->logger->error('Order expire handle error.', ['msg' => $th->getMessage()]);
        }
    }

    private function handle(OrderExpireMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumberWithLock($message->getNumber())
            ?? throw new UnrecoverableMessageHandlingException('Order not found.');

        if ($this->workflow->can($entity, $transitionName = OrderStateTransition::Expire->value)) {
            $this->workflow->apply($entity, $transitionName);
        }
    }
}
