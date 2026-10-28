<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

/**
 * Interface for entities that require purchase quantity validation.
 *
 * Architecture Note:
 * This decouples the Order domain from the Product domain.
 * By utilizing getGroupingKey(), the validator can perform both:
 * - Per-variant limits (by returning a unique variant/item ID as the key)
 * - Aggregated/mixed limits (by returning a shared parent product ID as the key)
 */
interface LimitableInterface
{
    /**
     * Returns the minimum required purchase quantity (minimum order limit).
     *
     * @return int|null the minimum quantity, or null if no minimum limit is set
     */
    public function getMinQuantity(): ?int;

    /**
     * Returns the maximum allowed purchase quantity (maximum order limit).
     *
     * @return int|null the maximum quantity, or null if no maximum limit is set
     */
    public function getMaxQuantity(): ?int;
}
