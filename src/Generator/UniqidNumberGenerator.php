<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Siganushka\OrderBundle\Entity\Order;

class UniqidNumberGenerator implements OrderNumberGeneratorInterface
{
    public function generate(Order $order): string
    {
        return \sprintf('%16s', hexdec(uniqid()));
    }
}
