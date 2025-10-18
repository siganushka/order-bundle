<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Stock;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Exception\InsufficientStockException;
use Siganushka\OrderBundle\Model\StockableInterface;

class OrderStockModifier implements OrderStockModifierInterface
{
    public const INCREMENT = 1;
    public const DECREMENT = 2;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function increment(Order $order): void
    {
        $this->modifiy($order, self::INCREMENT);
    }

    public function decrement(Order $order): void
    {
        $this->modifiy($order, self::DECREMENT);
    }

    private function modifiy(Order $order, int $action): void
    {
        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            if (!$subject instanceof StockableInterface) {
                continue;
            }

            $connection = $this->entityManager->getConnection();
            if ($connection->isTransactionActive()) {
                $this->entityManager->refresh($subject, LockMode::PESSIMISTIC_WRITE);
            }

            $stock = $subject->getAvailableStock();
            $quantity = $item->getQuantity();
            if (null === $stock || null === $quantity) {
                continue;
            }

            if (self::DECREMENT === $action && $quantity > $stock) {
                throw new InsufficientStockException($subject, $quantity);
            }

            match ($action) {
                self::INCREMENT => $subject->incrementStock($quantity),
                self::DECREMENT => $subject->decrementStock($quantity),
                default => throw new \UnhandledMatchError('The argument "action" is invalid.'),
            };
        }
    }
}
