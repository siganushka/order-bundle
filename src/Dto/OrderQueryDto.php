<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Dto;

use Siganushka\GenericBundle\Dto\DateRangeDto;
use Siganushka\OrderBundle\Enum\OrderState;

class OrderQueryDto
{
    public function __construct(
        public readonly ?string $number = null,
        public readonly ?OrderState $state = null,
        public readonly ?DateRangeDto $created = null,
    ) {
    }
}
