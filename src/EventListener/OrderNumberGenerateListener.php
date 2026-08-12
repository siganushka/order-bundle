<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;

class OrderNumberGenerateListener
{
    public function __construct(private readonly OrderNumberGeneratorInterface $numberGenerator)
    {
    }

    public function __invoke(AbstractOrder $entity): void
    {
        if (!$entity->getNumber()) {
            $entity->setNumber($this->numberGenerator->generate($entity));
        }
    }
}
