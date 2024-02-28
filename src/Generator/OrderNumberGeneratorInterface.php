<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Siganushka\OrderBundle\Entity\Order;

interface OrderNumberGeneratorInterface
{
    public function generate(Order $order): string;
}
