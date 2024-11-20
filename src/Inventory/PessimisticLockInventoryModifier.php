<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Inventory;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Exception\InsufficientInventoryException;

class PessimisticLockInventoryModifier implements OrderInventoryModifierInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function modifiy(int $action, Order $order): void
    {
        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            $quantity = $item->getQuantity();
            if (null === $subject || null === $quantity) {
                continue;
            }

            // Untracking inventory
            if (null === $subject->getInventory()) {
                continue;
            }

            // [important] Locking subject
            $connection = $this->entityManager->getConnection();
            if ($connection->isTransactionActive()) {
                $this->entityManager->refresh($subject, LockMode::PESSIMISTIC_WRITE);
            }

            // [important] Must be placed after locking
            $inventory = $subject->getInventory();
            if ($inventory < $quantity) {
                throw new InsufficientInventoryException($subject, $quantity);
            }

            $subject->setInventory(match ($action) {
                self::INCREASE => $inventory + $quantity,
                self::DECREASE => $inventory - $quantity,
                default => throw new \UnhandledMatchError('The argument "action" is invalid.'),
            });
        }
    }
}
