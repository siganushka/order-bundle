<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Godruoyi\Snowflake\Snowflake;
use Siganushka\OrderBundle\Entity\AbstractOrder;

class OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    public function __construct(private readonly Snowflake $snowflake = new Snowflake())
    {
    }

    public function generate(AbstractOrder $entity): string
    {
        return $this->snowflake->id();
    }
}
