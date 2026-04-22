<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Entity\Order;
use Symfony\Component\Security\Core\User\InMemoryUser;

/**
 * @extends Order<InMemoryUser, MyOrderItem, MyOrderAdjustment>
 */
class MyOrder extends Order
{
}
