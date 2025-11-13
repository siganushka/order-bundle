<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Dto\OrderQueryDto;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;

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
        $queryBuilder = $this->createQueryBuilderWithOrderBy($alias);

        if ($dto->state) {
            $queryBuilder->andWhere(\sprintf('%s.state = :state', $alias))->setParameter('state', $dto->state);
        }

        if ($dto->startAt) {
            $queryBuilder->andWhere(\sprintf('%s.createdAt >= :startAt', $alias))->setParameter('startAt', $dto->startAt);
        }

        if ($dto->endAt) {
            $queryBuilder->andWhere(\sprintf('%s.createdAt <= :endAt', $alias))->setParameter('endAt', $dto->endAt);
        }

        return $queryBuilder;
    }

    /**
     * @return array<value-of<OrderState>, int>
     */
    public function countByStateMapping(): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o.state, COUNT(o) as count')
            ->groupBy('o.state')
        ;

        $query = $queryBuilder->getQuery();
        $result = $query->getScalarResult();

        return array_column($result, 'count', 'state');
    }
}
