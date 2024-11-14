<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Modifier;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Entity\Order;

class AtomicUpdateInventoryModifier implements OrderInventoryModifierInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function modifiy(Order $order, int $action): void
    {
        $assignment = match ($action) {
            self::INCREMENT => 'entity.inventory + :quantity',
            self::DECREMENT => 'entity.inventory - :quantity',
        };

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

            $queryBuilder = $this->entityManager->createQueryBuilder()
                ->update($subject::class, 'entity')
                ->set('entity.inventory', $assignment)
                ->where('entity.id = :id')
                ->setParameter('id', $subject->getId(), ParameterType::INTEGER)
                ->setParameter('quantity', $quantity, ParameterType::INTEGER)
            ;

            // [important] Strict update
            if (self::DECREMENT === $action) {
                $queryBuilder->andWhere('entity.inventory >= :quantity');
            }

            $query = $queryBuilder->getQuery();
            if (!$query->execute()) {
                throw new \RuntimeException('Insufficient inventory.');
            }

            // Refresh subject after updated
            $this->entityManager->refresh($subject);
        }
    }
}
