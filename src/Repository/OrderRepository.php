<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Dto\OrderQueryDto;
use Siganushka\OrderBundle\Entity\Order;

/**
 * @template T of Order = Order
 *
 * @extends GenericEntityRepository<T>
 */
class OrderRepository extends GenericEntityRepository
{
    /**
     * @return T|null
     */
    public function findOneByNumber(string $number): ?Order
    {
        return $this->findOneBy(compact('number'));
    }

    /**
     * @return T|null
     */
    public function findOneByNumberWithLock(string $number): ?Order
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

    public function createQueryBuilderByDto(string $alias, OrderQueryDto $dto): QueryBuilder
    {
        $criteria = new Criteria(firstResult: 0, accessRawFieldValues: true);

        if ($dto->number) {
            $criteria->andWhere(Criteria::expr()->contains('number', $dto->number));
        }

        if ($dto->state) {
            $criteria->andWhere(Criteria::expr()->eq('state', $dto->state));
        }

        if ($dto->created?->startAt) {
            $criteria->andWhere(Criteria::expr()->gte('createdAt', $dto->created->startAt));
        }

        if ($dto->created?->endAt) {
            $criteria->andWhere(Criteria::expr()->lte('createdAt', $dto->created->endAt));
        }

        $qb = $this->createQueryBuilderWithOrderBy($alias);
        $qb->addCriteria($criteria);

        return $qb;
    }
}
