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

/**
 * @template TItem of AbstractOrderItem = AbstractOrderItem
 * @template TAdjustment of AbstractOrderAdjustment = AbstractOrderAdjustment
 */
#[ORM\MappedSuperclass(repositoryClass: OrderRepository::class)]
#[ORM\UniqueConstraint(columns: ['number'])]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractOrder implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    #[ORM\Column]
    protected ?string $number = null;

    #[ORM\Column]
    protected ?int $itemsTotal = null;

    #[ORM\Column]
    protected ?int $adjustmentsTotal = null;

    #[ORM\Column]
    protected ?int $total = null;

    #[ORM\Column(nullable: true)]
    protected ?string $note = null;

    #[ORM\Column(enumType: OrderState::class)]
    protected OrderState $state = OrderState::Pending;

    /**
     * @var Collection<int, TItem>
     */
    #[ORM\OneToMany(targetEntity: AbstractOrderItem::class, mappedBy: 'order', cascade: ['all'], orphanRemoval: true)]
    protected Collection $items;

    /**
     * @var Collection<int, TAdjustment>
     */
    #[ORM\OneToMany(targetEntity: AbstractOrderAdjustment::class, mappedBy: 'order', cascade: ['all'], orphanRemoval: true)]
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
        return $this->itemsTotal ??= $this->items->reduce(static fn (int $carry, AbstractOrderItem $item) => $carry + $item->getSubtotal(), 0);
    }

    public function getAdjustmentsTotal(): int
    {
        return $this->adjustmentsTotal ??= $this->adjustments->reduce(static fn (int $carry, AbstractOrderAdjustment $item) => $carry + $item->getAmount(), 0);
    }

    #[ORM\PreFlush]
    public function getTotal(): int
    {
        return $this->total ??= max(0, $this->getItemsTotal() + $this->getAdjustmentsTotal());
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getState(): OrderState
    {
        return $this->state;
    }

    public function setState(OrderState $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, TItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param TItem $item
     */
    public function addItem(AbstractOrderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->itemsTotal = $this->total = null;
            $this->items[] = $item;
            $item->setOrder($this);
        }

        return $this;
    }

    /**
     * @param TItem $item
     */
    public function removeItem(AbstractOrderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            $this->itemsTotal = $this->total = null;
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function clearItems(): static
    {
        $this->items->clear();
        $this->itemsTotal = $this->total = null;

        return $this;
    }

    /**
     * @return Collection<int, TAdjustment>
     */
    public function getAdjustments(): Collection
    {
        return $this->adjustments;
    }

    /**
     * @param TAdjustment $adjustment
     */
    public function addAdjustment(AbstractOrderAdjustment $adjustment): static
    {
        if (!$this->adjustments->contains($adjustment)) {
            $this->adjustmentsTotal = $this->total = null;
            $this->adjustments[] = $adjustment;
            $adjustment->setOrder($this);
        }

        return $this;
    }

    /**
     * @param TAdjustment $adjustment
     */
    public function removeAdjustment(AbstractOrderAdjustment $adjustment): static
    {
        if ($this->adjustments->removeElement($adjustment)) {
            $this->adjustmentsTotal = $this->total = null;
            if ($adjustment->getOrder() === $this) {
                $adjustment->setOrder(null);
            }
        }

        return $this;
    }

    public function clearAdjustments(): static
    {
        $this->adjustments->clear();
        $this->adjustmentsTotal = $this->total = null;

        return $this;
    }
}
