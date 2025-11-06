<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\CreatableTrait;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

#[ORM\Entity(repositoryClass: OrderAdjustmentRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
abstract class OrderAdjustment implements ResourceInterface, CreatableInterface
{
    use CreatableTrait;
    use ResourceTrait;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'adjustments')]
    protected ?Order $order = null;

    #[ORM\Column]
    protected ?int $amount = null;

    public function __construct(?int $amount = null)
    {
        $this->amount = $amount;
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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): string
    {
        $ref = new \ReflectionClass($this);
        /** @var string */
        $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());

        return strtolower($class);
    }

    public function getLabel(): TranslatableInterface
    {
        return new TranslatableMessage(\sprintf('order.adjustment.%s', $this->getType()));
    }
}
