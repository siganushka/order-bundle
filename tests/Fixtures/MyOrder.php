<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Entity\Order;

/**
 * @extends Order<MyOrderItem, MyOrderAdjustment>
 */
class MyOrder extends Order
{
}
