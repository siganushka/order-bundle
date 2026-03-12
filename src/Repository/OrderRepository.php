<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Doctrine\Common\Collections\Criteria;
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

    public function createQueryBuilderByDto(string $alias, OrderQueryDto $dto): QueryBuilder
    {
        $criteria = Criteria::create();

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

        $queryBuilder = $this->createQueryBuilderWithOrderBy($alias);
        $queryBuilder->addCriteria($criteria);

        return $queryBuilder;
    }
}
