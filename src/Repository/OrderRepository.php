<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Dto\OrderQueryDto;
use Siganushka\OrderBundle\Entity\AbstractOrder;

/**
 * @template T of AbstractOrder = AbstractOrder
 *
 * @extends GenericEntityRepository<T>
 */
class OrderRepository extends GenericEntityRepository
{
    /**
     * @return T|null
     */
    public function findOneByNumber(string $number): ?AbstractOrder
    {
        return $this->findOneBy(compact('number'));
    }

    /**
     * @return T|null
     */
    public function findOneByNumberWithLock(string $number): ?AbstractOrder
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.number = :number')
            ->setParameter('number', $number)
            ->setMaxResults(1)
        ;

        $query = $qb->getQuery();
        $query->setLockMode(LockMode::PESSIMISTIC_WRITE);

        /** @var T|null */
        $entity = $query->getOneOrNullResult();

        return $entity;
    }

    public function createQueryBuilderFromDto(string $alias, OrderQueryDto $dto): QueryBuilder
    {
        $criteria = self::createCriteriaFromDto($dto);

        $qb = $this->createQueryBuilderWithOrderBy($alias);
        $qb->addCriteria($criteria);

        return $qb;
    }
}
