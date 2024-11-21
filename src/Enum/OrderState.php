<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum OrderState: string implements TranslatableInterface
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipping = 'shipping';
    case Completed = 'completed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';

    public function theme(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'primary',
            self::Shipping => 'info',
            self::Completed => 'success',
            self::Refunded => 'secondary',
            self::Cancelled => 'secondary',
        };
    }

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans('order.state.'.$this->value, locale: $locale);
    }
}
