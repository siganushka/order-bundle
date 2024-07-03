<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Event;

use Siganushka\OrderBundle\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractOrderEvent extends Event
{
    public function __construct(private Order $order)
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
