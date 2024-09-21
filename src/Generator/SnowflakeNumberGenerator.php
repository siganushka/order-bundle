<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Godruoyi\Snowflake\Snowflake;
use Siganushka\OrderBundle\Entity\Order;

class SnowflakeNumberGenerator implements OrderNumberGeneratorInterface
{
    public function generate(Order $order): string
    {
        if (!class_exists(Snowflake::class)) {
            throw new \LogicException(\sprintf('The "%s" class requires the "godruoyi/php-snowflake" component. Try running "composer require godruoyi/php-snowflake".', self::class));
        }

        return (new Snowflake())->id();
    }
}
