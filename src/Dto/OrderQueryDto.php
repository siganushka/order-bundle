<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Dto;

use Siganushka\OrderBundle\Enum\OrderState;

class OrderQueryDto
{
    public function __construct(
        public readonly ?OrderState $state = null,
        public readonly ?\DateTimeInterface $startAt = null,
        public readonly ?\DateTimeInterface $endAt = null,
    ) {
    }
}
