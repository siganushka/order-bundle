<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Dto;

use Siganushka\GenericBundle\Dto\DateRangeDtoTrait;
use Siganushka\GenericBundle\Dto\PageQueryDtoTrait;
use Siganushka\OrderBundle\Enum\OrderState;

class OrderQueryDto
{
    use DateRangeDtoTrait;
    use PageQueryDtoTrait;

    public function __construct(public ?OrderState $state = null)
    {
    }
}
