<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Siganushka\OrderBundle\Entity\Order;

class TimestampNumberGenerator implements OrderNumberGeneratorInterface
{
    public function generate(Order $order): string
    {
        $microtime = microtime();

        return \sprintf('%10s%06s', substr($microtime, -10), substr($microtime, 2, 6));
    }
}
