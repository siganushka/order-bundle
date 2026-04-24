<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

class OrderItemSubjectData
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?int $price = null,
        public readonly ?string $subtitle = null,
        public readonly ?string $img = null,
    ) {
    }
}
