<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Message;

final class OrderExpireMessage
{
    public function __construct(private readonly string $number)
    {
    }

    public function getNumber(): string
    {
        return $this->number;
    }
}
