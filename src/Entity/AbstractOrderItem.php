<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\OrderBundle\Model\OrderItemSubjectSnapshot;
use Siganushka\OrderBundle\Repository\OrderItemRepository;

/**
 * @template TOrder of AbstractOrder = AbstractOrder
 * @template TSubject of OrderItemSubjectInterface = OrderItemSubjectInterface
 */
#[ORM\MappedSuperclass(repositoryClass: OrderItemRepository::class)]
#[ORM\UniqueConstraint(columns: ['order_id', 'subject_id'])]
abstract class AbstractOrderItem implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    protected ?int $id = null;

    /**
     * @var TOrder|null
     */
    #[ORM\ManyToOne(inversedBy: 'items')]
    protected ?AbstractOrder $order = null;

    /**
     * @var TSubject|null
     */
    #[ORM\ManyToOne]
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

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return TOrder|null
     */
    public function getOrder(): ?AbstractOrder
    {
        return $this->order;
    }

    /**
     * @param TOrder|null $order
     */
    public function setOrder(?AbstractOrder $order): static
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
        $this->updateSnapshot();

        return $this;
    }

    /**
     * Applies a snapshot to this order item. Useful for items without a subject.
     * Note: for items with a subject, the snapshot will be overwritten by the next updateSnapshot().
     */
    public function setSnapshot(OrderItemSubjectSnapshot $snapshot): static
    {
        $this->title = $snapshot->title;
        $this->subtitle = $snapshot->subtitle;
        $this->price = $snapshot->price;
        $this->img = $snapshot->img;

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
        $this->updateSnapshot();

        return $this;
    }

    public function getSubtotal(): int
    {
        return $this->price * $this->quantity;
    }

    private function updateSnapshot(): void
    {
        if ($this->subject && $this->quantity) {
            $this->setSnapshot($this->subject->getSubjectSnapshot($this->quantity));
        }
    }
}
