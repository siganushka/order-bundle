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
    private ?int $itemsTotal = null;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="order", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    public function getItemsTotal(): ?int
    {
        return $this->itemsTotal;
    }

    public function setItemsTotal(int $itemsTotal): self
    {
        $this->itemsTotal = $itemsTotal;

        return $this;
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
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }
}
