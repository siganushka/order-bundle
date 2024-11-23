<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Enum;

enum OrderStateTransition: string
{
    case Pay = 'pay';
    case Ship = 'ship';
    case Receive = 'receive';
    case Refund = 'refund';
    case Cancel = 'cancel';
    case Expire = 'expire';

    public function froms(): array
    {
        return match ($this) {
            self::Pay => [OrderState::Pending],
            self::Ship => [OrderState::Processing],
            self::Receive => [OrderState::Shipping],
            self::Refund => [OrderState::Processing, OrderState::Completed],
            self::Cancel => [OrderState::Pending, OrderState::Processing],
            self::Expire => [OrderState::Pending],
        };
    }

    public function tos(): array
    {
        return match ($this) {
            self::Pay => [OrderState::Processing],
            self::Ship => [OrderState::Shipping],
            self::Receive => [OrderState::Completed],
            self::Refund => [OrderState::Refunded],
            self::Cancel => [OrderState::Cancelled],
            self::Expire => [OrderState::Cancelled],
        };
    }
}
