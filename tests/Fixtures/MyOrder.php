<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Entity\AbstractOrder;

/**
 * @extends AbstractOrder<MyOrderItem, MyOrderAdjustment>
 */
class MyOrder extends AbstractOrder
{
}
