<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Dto;

use Siganushka\OrderBundle\Enum\OrderState;

class OrderFilterDto
{
    public ?OrderState $state = null;

    public ?\DateTimeInterface $startAt = null;

    public ?\DateTimeInterface $endAt = null;
}
