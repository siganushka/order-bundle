<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Entity\OrderAdjustment;

/**
 * @template T of OrderAdjustment = OrderAdjustment
 *
 * @extends GenericEntityRepository<T>
 */
class OrderAdjustmentRepository extends GenericEntityRepository
{
}
