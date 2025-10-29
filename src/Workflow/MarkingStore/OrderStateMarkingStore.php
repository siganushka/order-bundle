<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Workflow\MarkingStore;

use Siganushka\OrderBundle\Entity\Order;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

/**
 * @see https://github.com/symfony/symfony/pull/62199
 */
class OrderStateMarkingStore implements MarkingStoreInterface
{
    /**
     * @param Order $subject
     */
    public function getMarking(object $subject): Marking
    {
        return new Marking([$subject->getStateAsString() => 1]);
    }

    /**
     * @param Order $subject
     */
    public function setMarking(object $subject, Marking $marking, array $context = []): void
    {
        $stateAsString = key($marking->getPlaces());
        \assert(\is_string($stateAsString));

        $subject->setStateAsString($stateAsString);
    }
}
