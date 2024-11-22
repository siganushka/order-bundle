<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Entity\Order;

/**
 * @extends GenericEntityRepository<Order>
 */
class OrderRepository extends GenericEntityRepository
{
    public function findOneByNumber(string $number): ?Order
    {
        return $this->findOneBy(compact('number'));
    }
}
