<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

use Siganushka\Contracts\Doctrine\ResourceInterface;

interface OrderItemSubjectInterface extends ResourceInterface
{
    /**
     * Returns the name.
     */
    public function getName(): ?string;

    /**
     * Returns the unit price.
     */
    public function getPrice(): ?int;

    /**
     * Returns the current inventory quantity. If it is null, it will not be tracked.
     */
    public function getInventory(): ?int;

    /**
     * Set current inventory quantity.
     */
    public function setInventory(?int $inventory): static;
}
