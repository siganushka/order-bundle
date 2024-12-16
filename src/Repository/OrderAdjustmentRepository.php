<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Entity\OrderAdjustment;

/**
 * @template T of OrderAdjustment = OrderAdjustment
 *
 * @extends GenericEntityRepository<T>
 */
class OrderAdjustmentRepository extends GenericEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        /** @var class-string<T> */
        $entityClass = OrderAdjustment::class;
        parent::__construct($registry, $entityClass);
    }
}
