<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Entity\OrderItem;

/**
 * @template T of OrderItem = OrderItem
 *
 * @extends GenericEntityRepository<T>
 */
class OrderItemRepository extends GenericEntityRepository
{
}
