<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\CreatableTrait;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\GenericBundle\Utils\ClassUtils;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;

/**
 * @template TOrder of Order = Order
 */
#[ORM\Entity(repositoryClass: OrderAdjustmentRepository::class, readOnly: true)]
#[ORM\InheritanceType('SINGLE_TABLE')]
abstract class OrderAdjustment implements ResourceInterface, CreatableInterface
{
    use CreatableTrait;
    use ResourceTrait;

    /**
     * @var TOrder|null
     */
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'adjustments')]
    protected ?Order $order = null;

    #[ORM\Column]
    protected ?int $amount;

    public function __construct(?int $amount = null)
    {
        $this->amount = $amount;
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
