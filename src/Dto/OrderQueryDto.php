<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Dto;

use Doctrine\Common\Collections\Expr\Comparison;
use Siganushka\GenericBundle\Attribute\QueryFilter;
use Siganushka\GenericBundle\Dto\DateRangeDto;
use Siganushka\OrderBundle\Enum\OrderState;

class OrderQueryDto
{
    public function __construct(
        #[QueryFilter(expr: Comparison::CONTAINS)]
        public readonly ?string $number = null,
        #[QueryFilter(expr: Comparison::CONTAINS)]
        public readonly ?string $note = null,
        #[QueryFilter]
        public readonly ?OrderState $state = null,
        #[QueryFilter(expr: ['startAt' => Comparison::GTE, 'endAt' => Comparison::LTE])]
        public readonly ?DateRangeDto $createdAt = null,
    ) {
    }
}
