<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;

#[AsEntityListener(event: Events::prePersist, entity: Order::class)]
class OrderNumberGenerateListener
{
    public function __construct(protected readonly OrderNumberGeneratorInterface $generator)
    {
    }

    public function prePersist(Order $entity): void
    {
        $number = $entity->getNumber();
        if (null === $number) {
            $entity->setNumber($this->generator->generate($entity));
        }
    }
}
