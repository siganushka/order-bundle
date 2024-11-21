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
    case Reset = 'reset';

    public function froms(): array
    {
        return match ($this) {
            self::Pay => [OrderState::Placed],
            self::Ship => [OrderState::Confirmed],
            self::Receive => [OrderState::Shipped],
            self::Refund => [OrderState::Confirmed, OrderState::Shipped, OrderState::Completed],
            self::Cancel => [OrderState::Placed],
            self::Reset => [OrderState::Confirmed, OrderState::Shipped, OrderState::Completed],
        };
    }

    public function tos(): array
    {
        return match ($this) {
            self::Pay => [OrderState::Confirmed],
            self::Ship => [OrderState::Shipped],
            self::Receive => [OrderState::Completed],
            self::Refund => [OrderState::Refunded],
            self::Cancel => [OrderState::Cancelled],
            self::Reset => [OrderState::Placed],
        };
    }
}
