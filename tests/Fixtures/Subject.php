<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Model\OrderItemSubjectData;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;

class Subject implements OrderItemSubjectInterface
{
    public function __construct(
        protected readonly int $id,
        protected readonly string $title,
        protected readonly int $price,
        protected readonly ?string $subtitle = null,
        protected readonly ?string $img = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function createForOrderItem(int $quantity): OrderItemSubjectData
    {
        return new OrderItemSubjectData(
            identifier: $this->id,
            title: $this->title,
            subtitle: $this->subtitle,
            price: $this->price,
            img: $this->img,
        );
    }
}
