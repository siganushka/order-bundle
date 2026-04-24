<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

use Siganushka\Contracts\Doctrine\ResourceInterface;

interface OrderItemSubjectInterface extends ResourceInterface
{
    /**
     * Create an immutable snapshot of the subject data for an order item.
     * This ensures the order record remains consistent even if the source
     * resource (e.g., product price or name) changes later.
     *
     * @param int $quantity For internal volume-based pricing. Ignore if not applicable.
     *
     * @return OrderItemSubjectData a snapshot of the source data to be persisted within the order item
     */
    public function createForOrderItem(int $quantity): OrderItemSubjectData;
}
