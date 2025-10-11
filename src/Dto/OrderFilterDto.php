<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Dto;

use Siganushka\GenericBundle\Dto\DateRangeDtoTrait;
use Siganushka\GenericBundle\Dto\PageQueryDtoTrait;
use Siganushka\OrderBundle\Enum\OrderState;

class OrderFilterDto
{
    use DateRangeDtoTrait;
    use PageQueryDtoTrait;

    public ?OrderState $state = null;
}
