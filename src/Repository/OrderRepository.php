<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
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
}
