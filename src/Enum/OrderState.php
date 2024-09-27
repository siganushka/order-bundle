<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum OrderState: string implements TranslatableInterface
{
    case Placed = 'placed';
    case Accepted = 'accepted';
    case Shipped = 'shipped';
    case Finished = 'finished';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';

    public function theme(): string
    {
        return match ($this) {
            self::Placed => 'warning',
            self::Accepted => 'primary',
            self::Shipped => 'info',
            self::Finished => 'success',
            self::Refunded => 'secondary',
            self::Cancelled => 'secondary',
        };
    }

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans('order.state.'.$this->value, locale: $locale);
    }
}
