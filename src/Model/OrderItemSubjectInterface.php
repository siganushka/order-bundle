<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

interface OrderItemSubjectInterface
{
    /**
     * Returns an unique identifier for the subject.
     *
     * @return int|string subject identifier
     */
    public function getSubjectIdentifier(): int|string;

    /**
     * Returns an immutable snapshot of the subject data for an order item.
     * This ensures the order record remains consistent even if the source
     * resource (e.g., product price or name) changes later.
     *
     * @param int $quantity for internal volume-based pricing. ignore if not applicable.
     *
     * @return OrderItemSubjectSnapshot a snapshot of the source data to be persisted within the order item
     */
    public function getSubjectSnapshot(int $quantity): OrderItemSubjectSnapshot;
}
