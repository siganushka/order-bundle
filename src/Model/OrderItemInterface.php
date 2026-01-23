<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

interface OrderItemInterface
{
    public function getTitle(): string;

    public function getSubtitle(): ?string;

    public function getImg(): ?string;

    public function getPrice(): int;

    public function getQuantity(): int;

    public function getSubtotal(): int;
}
