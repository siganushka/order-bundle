<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
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
