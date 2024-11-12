<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Modifier;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Entity\Order;

class PessimisticLockingInventoryModifier implements OrderInventoryModifierInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function modifiy(Order $order, int $action): void
    {
        $this->entityManager->beginTransaction();
        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            $quantity = $item->getQuantity();
            if (null === $subject || null === $quantity) {
                throw new \InvalidArgumentException('The subject or quantity cannot be null.');
            }

            // Untracking inventory
            if (null === $subject->getInventory()) {
                continue;
            }

            $this->entityManager->refresh($subject, LockMode::PESSIMISTIC_WRITE);

            $subject->setInventory(match ($action) {
                self::INCREMENT => $subject->getInventory() + $quantity,
                self::DECREMENT => $subject->getInventory() - $quantity,
            });
        }

        $this->entityManager->commit();
    }
}
