<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Entity\AbstractOrderItem;

/**
 * @extends AbstractOrderItem<MyOrder, Subject>
 */
class MyOrderItem extends AbstractOrderItem
{
}
