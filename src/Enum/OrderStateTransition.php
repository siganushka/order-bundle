<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Enum;

enum OrderStateTransition: string
{
    case Confirm = 'confirm';
    case Ship = 'ship';
    case Deliver = 'deliver';
    case Cancel = 'cancel';
    case Expire = 'expire';

    public function froms(): array
    {
        return match ($this) {
            self::Confirm => [OrderState::Pending],
            self::Ship => [OrderState::Processing],
            self::Deliver => [OrderState::Shipping],
            self::Cancel => [OrderState::Pending, OrderState::Processing],
            self::Expire => [OrderState::Pending],
        };
    }

    public function tos(): array
    {
        return match ($this) {
            self::Confirm => [OrderState::Processing],
            self::Ship => [OrderState::Shipping],
            self::Deliver => [OrderState::Completed],
            self::Cancel,
            self::Expire => [OrderState::Cancelled],
        };
    }
}
