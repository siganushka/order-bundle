<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Model\QuantityAwareSubjectInterface;

class QuantityAwareSubject extends Subject implements QuantityAwareSubjectInterface
{
    public function getSubjectPriceByQuantity(int $quantity): int
    {
        return match (true) {
            $quantity >= 10000 => 50,
            $quantity >= 1000 => 80,
            $quantity >= 100 => 90,
            default => $this->price,
        };
    }
}
