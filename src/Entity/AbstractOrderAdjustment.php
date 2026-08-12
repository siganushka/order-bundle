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
 * @template TOrder of AbstractOrder = AbstractOrder
 */
#[ORM\Entity(repositoryClass: OrderAdjustmentRepository::class)]
#[ORM\Table(name: 'order_adjustment')]
#[ORM\InheritanceType(value: 'SINGLE_TABLE')]
abstract class AbstractOrderAdjustment implements ResourceInterface, CreatableInterface
{
    use CreatableTrait;
    use ResourceTrait;

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
