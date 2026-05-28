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
#[ORM\Entity(repositoryClass: OrderItemRepository::class, readOnly: true)]
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
     * @var TSubject|null
     */
    #[ORM\ManyToOne(targetEntity: OrderItemSubjectInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    protected ?OrderItemSubjectInterface $subject = null;

    #[ORM\Column]
    protected ?string $title = null;

    #[ORM\Column(nullable: true)]
    protected ?string $subtitle = null;

    #[ORM\Column(nullable: true)]
    protected ?string $img = null;

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
        $this->update();

        return $this;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;
        $this->update();

        return $this;
    }

    public function getSubtotal(): int
    {
        return $this->price * $this->quantity;
    }

    public function update(): void
    {
        if (!$this->subject || !$this->quantity) {
            return;
        }

        $subjectData = $this->subject->createForOrderItem($this->quantity);

        [
            $this->title,
            $this->subtitle,
            $this->img,
            $this->price,
        ] = [
            $subjectData->title,
            $subjectData->subtitle,
            $subjectData->img,
            $subjectData->price,
        ];
    }
}
