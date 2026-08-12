<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Entity\AbstractOrderItem;

/**
 * @template T of AbstractOrderItem = AbstractOrderItem
 *
 * @extends GenericEntityRepository<T>
 */
class OrderItemRepository extends GenericEntityRepository
{
}
