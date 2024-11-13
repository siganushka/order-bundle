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

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\UniqueConstraint(columns: ['order_id', 'subject_id'])]
class OrderItem implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected ?ProductVariant $subject = null;

    #[ORM\Column]
    protected ?int $unitPrice = null;

    #[ORM\Column]
    protected ?int $quantity = null;

    public function __construct(ProductVariant $subject = null, int $quantity = null)
    {
        $this->setSubject($subject);
        $this->setQuantity($quantity);
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getSubject(): ?ProductVariant
    {
        return $this->subject;
    }

    public function setSubject(?ProductVariant $subject): static
    {
        $this->subject = $subject;

        if ($subject instanceof ProductVariant) {
            $this->unitPrice = $subject->getPrice();
        }

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): static
    {
        throw new \BadMethodCallException('The unitPrice cannot be modified anymore.');
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
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
