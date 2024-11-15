<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

use Siganushka\Contracts\Doctrine\ResourceInterface;

interface OrderItemSubjectInterface extends ResourceInterface
{
    /**
     * Returns the subject name.
     */
    public function getName(): ?string;

    /**
     * Returns the unit price.
     */
    public function getPrice(): ?int;

    /**
     * Returns the current inventory quantity. Untracking If it returns null.
     */
    public function getInventory(): ?int;

    /**
     * Settings current inventory.
     */
    public function setInventory(?int $inventory): static;
}
