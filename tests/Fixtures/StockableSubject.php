<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Model\StockableInterface;

class StockableSubject extends Subject implements StockableInterface
{
    private ?int $stock;

    public function __construct(?int $id, string $title, int $price, ?string $extra = null, ?string $img = null, ?int $stock = null)
    {
        parent::__construct($id, $title, $price, $extra, $img);

        $this->stock = $stock;
    }

    public function availableStock(): ?int
    {
        return $this->stock;
    }

    public function incrementStock(int $quantity): void
    {
        $this->stock += $quantity;
    }

    public function decrementStock(int $quantity): void
    {
        $this->stock -= $quantity;
    }
}
