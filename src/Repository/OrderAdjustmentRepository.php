<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Entity\OrderAdjustment;

/**
 * @extends ServiceEntityRepository<OrderAdjustment>
 *
 * @method OrderAdjustment      createNew(...$args)
 * @method OrderAdjustment|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderAdjustment|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderAdjustment[]    findAll()
 * @method OrderAdjustment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderAdjustmentRepository extends GenericEntityRepository
{
}
