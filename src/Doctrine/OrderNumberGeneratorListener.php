<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Doctrine;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;

class OrderNumberGeneratorListener
{
    public function __construct(private readonly OrderNumberGeneratorInterface $numberGenerator)
    {
    }

    public function prePersist(Order $entity): void
    {
        if (!$entity->getNumber()) {
            $entity->setNumber($this->numberGenerator->generate($entity));
        }
    }
}
