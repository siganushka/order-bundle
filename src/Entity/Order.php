<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\OrderBundle\Repository\OrderRepository;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", length=16, options={"fixed": true})
     */
    private ?string $number = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $itemsTotal = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private int $adjustmentsTotal = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private int $total = 0;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="order", cascade={"all"}, orphanRemoval=true)
     *
     * @var Collection<int, OrderItem>
     */
    private Collection $items;

    /**
     * @ORM\OneToMany(targetEntity=OrderAdjustment::class, mappedBy="order", cascade={"all"}, orphanRemoval=true)
     *
     * @var Collection<int, OrderAdjustment>
     */
    private Collection $adjustments;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->adjustments = new ArrayCollection();
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getItemsTotal(): int
    {
        return $this->itemsTotal;
    }

    public function setItemsTotal(int $itemsTotal): self
    {
        throw new \BadMethodCallException('The itemsTotal cannot be modified anymore.');
    }

    public function getAdjustmentsTotal(): int
    {
        return $this->adjustmentsTotal;
    }

    public function setAdjustmentsTotal(int $adjustmentsTotal): self
    {
        throw new \BadMethodCallException('The adjustmentsTotal cannot be modified anymore.');
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        throw new \BadMethodCallException('The total cannot be modified anymore.');
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrder($this);
            $this->recalculateItemsTotal();
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            $this->recalculateItemsTotal();
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function clearItems(): self
    {
        $this->items->clear();
        $this->recalculateItemsTotal();

        return $this;
    }

    public function countItems(): int
    {
        return $this->items->count();
    }

    /**
     * @return Collection<int, OrderAdjustment>
     */
    public function getAdjustments(): Collection
    {
        return $this->adjustments;
    }

    public function addAdjustment(OrderAdjustment $adjustment): self
    {
        if (!$this->adjustments->contains($adjustment)) {
            $this->adjustments[] = $adjustment;
            $adjustment->setOrder($this);
            $this->recalculateAdjustmentsTotal();
        }

        return $this;
    }

    public function removeAdjustment(OrderAdjustment $adjustment): self
    {
        if ($this->adjustments->removeElement($adjustment)) {
            $this->recalculateAdjustmentsTotal();
            if ($adjustment->getOrder() === $this) {
                $adjustment->setOrder(null);
            }
        }

        return $this;
    }

    public function clearAdjustments(): self
    {
        $this->adjustments->clear();
        $this->recalculateAdjustmentsTotal();

        return $this;
    }

    public function countAdjustments(): int
    {
        return $this->adjustments->count();
    }

    public function recalculateItemsTotal(): self
    {
        $this->itemsTotal = array_reduce($this->items->toArray(), fn (int $carry, OrderItem $item) => $carry + ($item->getSubtotal() ?? 0), 0);
        $this->recalculateTotal();

        return $this;
    }

    public function recalculateAdjustmentsTotal(): self
    {
        $this->adjustmentsTotal = array_reduce($this->adjustments->toArray(), fn (int $carry, OrderAdjustment $adjustment) => $carry + ($adjustment->getAmount() ?? 0), 0);
        $this->recalculateTotal();

        return $this;
    }

    public function recalculateTotal(): self
    {
        $this->total = $this->itemsTotal + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }

        return $this;
    }

    /**
     * Returns whether it is a free order.
     */
    public function isFree(): bool
    {
        return $this->total <= 0;
    }
}
