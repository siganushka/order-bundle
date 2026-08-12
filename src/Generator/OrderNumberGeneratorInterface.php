<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Siganushka\OrderBundle\Entity\AbstractOrder;

interface OrderNumberGeneratorInterface
{
    public function generate(AbstractOrder $entity): string;
}
