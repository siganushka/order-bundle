<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Godruoyi\Snowflake\Snowflake;
use Siganushka\OrderBundle\Entity\Order;

class SnowflakeNumberGenerator implements OrderNumberGeneratorInterface
{
    public function generate(Order $order): string
    {
        return (new Snowflake())->id();
    }
}
