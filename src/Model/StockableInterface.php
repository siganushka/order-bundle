<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

interface StockableInterface
{
    public function availableStock(): ?int;

    public function incrementStock(int $quantity): void;

    public function decrementStock(int $quantity): void;
}
