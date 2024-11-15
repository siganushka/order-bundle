<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Exception;

use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;

class InsufficientInventoryException extends \RuntimeException
{
    public function __construct(private readonly OrderItemSubjectInterface $subject, private readonly int $quantity)
    {
        parent::__construct('Insufficient inventory.');
    }

    public function getSubject(): OrderItemSubjectInterface
    {
        return $this->subject;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
