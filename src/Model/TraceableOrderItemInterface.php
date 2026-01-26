<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

interface TraceableOrderItemInterface extends OrderItemInterface
{
    public function getSubject(): ?OrderItemSubjectInterface;

    public function getSubjectId(): int|string|null;
}
