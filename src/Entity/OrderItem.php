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

    #[ORM\Column(nullable: true)]
    protected ?string $title;

    #[ORM\Column(nullable: true)]
    protected ?string $subtitle;

    #[ORM\Column(nullable: true)]
    protected ?string $img;

    #[ORM\Column]
    protected ?int $price;

    #[ORM\Column]
    protected int $quantity;

    /**
     * @param TSubject $subject
     */
    public function __construct(OrderItemSubjectInterface $subject, int $quantity)
    {
        $subjectData = $subject->createForOrderItem($quantity);

        [
            $this->subject,
            $this->quantity,
            $this->title,
            $this->subtitle,
            $this->img,
            $this->price,
        ] = [
            $subject,
            $quantity,
            $subjectData->title,
            $subjectData->subtitle,
            $subjectData->img,
            $subjectData->price,
        ];
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

    public function getSubjectId(): ?int
    {
        return $this->subject->getId();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSubtotal(): int
    {
        return $this->price * $this->quantity;
    }
}
