<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Siganushka\ProductBundle\Entity\ProductVariant;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 * @ORM\Table(uniqueConstraints={
 *  @ORM\UniqueConstraint(columns={"order_id", "variant_id"})
 * })
 */
class OrderItem implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Order $order = null;

    /**
     * @ORM\ManyToOne(targetEntity=ProductVariant::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ProductVariant $variant = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $unitPrice = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $quantity = null;

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getVariant(): ?ProductVariant
    {
        return $this->variant;
    }

    public function setVariant(?ProductVariant $variant): self
    {
        $this->variant = $variant;

        if ($variant instanceof ProductVariant) {
            $this->unitPrice = $variant->getPrice();
        }

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): self
    {
        throw new \BadMethodCallException('The unitPrice cannot be modified anymore.');
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSubtotal(): ?int
    {
        if (null === $this->unitPrice || null === $this->quantity) {
            return null;
        }

        return $this->unitPrice * $this->quantity;
    }
}
