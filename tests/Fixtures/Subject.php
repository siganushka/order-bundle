<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\OrderBundle\Model\OrderItemSubjectSnapshot;

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

    public function getSubjectIdentifier(): int|string
    {
        return $this->id;
    }

    public function getSubjectSnapshot(int $quantity): OrderItemSubjectSnapshot
    {
        return new OrderItemSubjectSnapshot(
            title: $this->title,
            subtitle: $this->subtitle,
            price: $this->price,
            img: $this->img,
        );
    }
}
