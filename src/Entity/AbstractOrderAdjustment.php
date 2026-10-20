<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\CreatableTrait;
use Siganushka\GenericBundle\Utils\ClassUtils;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;

/**
 * @template TOrder of AbstractOrder = AbstractOrder
 */
#[ORM\MappedSuperclass(repositoryClass: OrderAdjustmentRepository::class)]
abstract class AbstractOrderAdjustment implements CreatableInterface
{
    use CreatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    /**
     * @var TOrder|null
     */
    #[ORM\ManyToOne(inversedBy: 'adjustments')]
    protected ?AbstractOrder $order = null;

    #[ORM\Column]
    protected ?int $amount;

    public function __construct(?int $amount = null)
    {
        $this->amount = $amount;
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

    public function getAmount(): int
    {
        return $this->amount ?? 0;
    }

    public function setAmount(?int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): string
    {
        return ClassUtils::generateAlias($this);
    }
}
