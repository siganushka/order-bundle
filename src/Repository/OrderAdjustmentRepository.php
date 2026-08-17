<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Entity\AbstractOrderAdjustment;

/**
 * @template T of AbstractOrderAdjustment = AbstractOrderAdjustment
 *
 * @extends GenericEntityRepository<T>
 */
class OrderAdjustmentRepository extends GenericEntityRepository
{
}
