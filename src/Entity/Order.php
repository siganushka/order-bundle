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
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Repository\OrderRepository;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\UniqueConstraint(columns: ['number'])]
class Order implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    #[ORM\Column]
    protected ?string $number = null;

    #[ORM\Column]
    protected int $itemsTotal = 0;

    #[ORM\Column]
    protected int $adjustmentsTotal = 0;

    #[ORM\Column]
    protected int $total = 0;

    #[ORM\Column(enumType: OrderState::class)]
    protected OrderState $state = OrderState::Pending;

    #[ORM\Column(nullable: true)]
    protected ?string $note = null;

    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['all'], orphanRemoval: true)]
    protected Collection $items;

    /** @var Collection<int, OrderAdjustment> */
    #[ORM\OneToMany(targetEntity: OrderAdjustment::class, mappedBy: 'order', cascade: ['all'], orphanRemoval: true)]
    protected Collection $adjustments;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->adjustments = new ArrayCollection();
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getItemsTotal(): int
    {
        return $this->itemsTotal;
    }

    public function setItemsTotal(int $itemsTotal): static
    {
        throw new \BadMethodCallException('The itemsTotal cannot be modified anymore.');
    }

    public function getAdjustmentsTotal(): int
    {
        return $this->adjustmentsTotal;
    }

    public function setAdjustmentsTotal(int $adjustmentsTotal): static
    {
        throw new \BadMethodCallException('The adjustmentsTotal cannot be modified anymore.');
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        throw new \BadMethodCallException('The total cannot be modified anymore.');
    }

    public function getState(): OrderState
    {
        return $this->state;
    }

    public function setState(OrderState $state): static
    {
        throw new \BadMethodCallException('The state cannot be set manually.');
    }

    public function getStateAsString(): string
    {
        return $this->state->value;
    }

    public function setStateAsString(string $stateAsString): static
    {
        $this->state = OrderState::from($stateAsString);

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrder($this);
            $this->recalculateItemsTotal();
        }

        return $this;
    }

    public function removeItem(OrderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            $this->recalculateItemsTotal();
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function clearItems(): static
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

    public function addAdjustment(OrderAdjustment $adjustment): static
    {
        if (!$this->adjustments->contains($adjustment)) {
            $this->adjustments[] = $adjustment;
            $adjustment->setOrder($this);
            $this->recalculateAdjustmentsTotal();
        }

        return $this;
    }

    public function removeAdjustment(OrderAdjustment $adjustment): static
    {
        if ($this->adjustments->removeElement($adjustment)) {
            $this->recalculateAdjustmentsTotal();
            if ($adjustment->getOrder() === $this) {
                $adjustment->setOrder(null);
            }
        }

        return $this;
    }

    public function clearAdjustments(): static
    {
        $this->adjustments->clear();
        $this->recalculateAdjustmentsTotal();

        return $this;
    }

    public function countAdjustments(): int
    {
        return $this->adjustments->count();
    }

    public function recalculateItemsTotal(): static
    {
        $this->itemsTotal = $this->items->reduce(fn (int $carry, OrderItem $item) => $carry + ($item->getSubtotal() ?? 0), 0);
        $this->recalculateTotal();

        return $this;
    }

    public function recalculateAdjustmentsTotal(): static
    {
        $this->adjustmentsTotal = $this->adjustments->reduce(fn (int $carry, OrderAdjustment $adjustment) => $carry + ($adjustment->getAmount() ?? 0), 0);
        $this->recalculateTotal();

        return $this;
    }

    public function recalculateTotal(): static
    {
        $this->total = $this->itemsTotal + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }

        return $this;
    }
}
