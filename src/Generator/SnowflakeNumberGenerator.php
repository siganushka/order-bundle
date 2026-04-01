<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Godruoyi\Snowflake\Snowflake;
use Siganushka\OrderBundle\Entity\Order;

class SnowflakeNumberGenerator implements OrderNumberGeneratorInterface
{
    private readonly Snowflake $snowflake;

    public function __construct(?Snowflake $snowflake = null)
    {
        $this->snowflake = $snowflake ?? new Snowflake();
    }

    public function generate(Order $order): string
    {
        return $this->snowflake->id();
    }
}
