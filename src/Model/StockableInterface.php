<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

interface StockableInterface
{
    public function getAvailableStock(): ?int;

    public function decrementStock(int $quantity): void;

    public function incrementStock(int $quantity): void;
}
