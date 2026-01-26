<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Fixtures;

use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;

class Subject implements OrderItemSubjectInterface
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $title,
        private readonly int $price,
        private readonly ?string $subtitle = null,
        private readonly ?string $img = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubjectTitle(): string
    {
        return $this->title;
    }

    public function getSubjectSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function getSubjectPrice(): int
    {
        return $this->price;
    }

    public function getSubjectImg(): ?string
    {
        return $this->img;
    }
}
