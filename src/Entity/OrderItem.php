<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\OrderBundle\Repository\OrderItemRepository;

/**
 * @template TSubject of OrderItemSubjectInterface = OrderItemSubjectInterface
 */
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\UniqueConstraint(columns: ['order_id', 'subject_id'])]
class OrderItem implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    protected ?Order $order = null;

    /**
     * @var TSubject|null
     */
    #[ORM\ManyToOne(targetEntity: OrderItemSubjectInterface::class)]
    protected ?OrderItemSubjectInterface $subject = null;

    #[ORM\Column]
    protected ?int $price = null;

    #[ORM\Column]
    protected ?int $quantity = null;

    /**
     * @param TSubject|null $subject
     */
    public function __construct(?OrderItemSubjectInterface $subject = null, ?int $quantity = null)
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

    /**
     * @return TSubject|null
     */
    public function getSubject(): ?OrderItemSubjectInterface
    {
        return $this->subject;
    }

    /**
     * @param TSubject|null $subject
     */
    public function setSubject(?OrderItemSubjectInterface $subject): static
    {
        $this->subject = $subject;
        $this->price = $subject?->getSubjectPrice();

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        throw new \BadMethodCallException('The price cannot be modified anymore.');
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
        if (\is_int($this->price) && \is_int($this->quantity)) {
            return $this->price * $this->quantity;
        }

        return null;
    }

    public function getSubjectId(): int|string|null
    {
        return $this->subject?->getId();
    }

    public function getSubjectTitle(): ?string
    {
        return $this->subject?->getSubjectTitle();
    }

    public function getSubjectSubtitle(): ?string
    {
        return $this->subject?->getSubjectSubtitle();
    }

    public function getSubjectImg(): ?string
    {
        return $this->subject?->getSubjectImg();
    }
}
