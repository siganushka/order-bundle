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
        parent::__construct(\sprintf('Resource #%d Out of Stock.', $subject->getId()));
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
