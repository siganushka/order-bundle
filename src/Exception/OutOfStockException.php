<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Exception;

use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\OrderBundle\Model\StockableInterface;

class OutOfStockException extends \RuntimeException
{
    public function __construct(
        private readonly OrderItemSubjectInterface&StockableInterface $subject,
        private readonly int $quantity,
    ) {
        parent::__construct(\sprintf('Resource "%s" Out of Stock.', $subject->createForOrderItem($quantity)->title));
    }

    public function getSubject(): OrderItemSubjectInterface&StockableInterface
    {
        return $this->subject;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
