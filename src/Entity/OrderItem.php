<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\OrderBundle\Model\QuantityAwareSubjectInterface;
use Siganushka\OrderBundle\Repository\OrderItemRepository;

/**
 * @template TOrder of Order = Order
 * @template TSubject of OrderItemSubjectInterface = OrderItemSubjectInterface
 */
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\UniqueConstraint(columns: ['order_id', 'subject_id'])]
class OrderItem implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    /**
     * @var TOrder|null
     */
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    protected ?Order $order = null;

    /**
     * @var TSubject
     */
    #[ORM\ManyToOne(targetEntity: OrderItemSubjectInterface::class)]
    protected OrderItemSubjectInterface $subject;

    #[ORM\Column]
    protected int $price;

    #[ORM\Column]
    protected int $quantity;

    /**
     * @param TSubject $subject
     */
    public function __construct(OrderItemSubjectInterface $subject, int $quantity)
    {
        $this->subject = $subject;
        $this->quantity = $quantity;

        $this->price = $subject instanceof QuantityAwareSubjectInterface
            ? $subject->getSubjectPriceByQuantity($quantity)
            : $subject->getSubjectPrice();
    }

    /**
     * @return TOrder|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param TOrder|null $order
     */
    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return TSubject
     */
    public function getSubject(): OrderItemSubjectInterface
    {
        return $this->subject;
    }

    /**
     * @param TSubject $subject
     */
    public function setSubject(OrderItemSubjectInterface $subject): static
    {
        throw new \BadMethodCallException('The subject cannot be modified anymore.');
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        throw new \BadMethodCallException('The price cannot be modified anymore.');
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        throw new \BadMethodCallException('The quantity cannot be modified anymore.');
    }

    public function getSubtotal(): int
    {
        return $this->price * $this->quantity;
    }

    public function getSubjectId(): int|string|null
    {
        return $this->subject->getId();
    }

    public function getSubjectTitle(): ?string
    {
        return $this->subject->getSubjectTitle();
    }

    public function getSubjectSubtitle(): ?string
    {
        return $this->subject->getSubjectSubtitle();
    }

    public function getSubjectImg(): ?string
    {
        return $this->subject->getSubjectImg();
    }
}
