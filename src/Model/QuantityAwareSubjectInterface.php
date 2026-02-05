<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

use Siganushka\Contracts\Doctrine\ResourceInterface;

interface QuantityAwareSubjectInterface extends ResourceInterface
{
    public function getSubjectPriceByQuantity(int $quantity): int;
}
