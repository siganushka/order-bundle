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
    case Cancelled = 'cancelled';

    public function theme(): string
    {
        return match ($this) {
            self::Pending => 'primary',
            self::Processing => 'warning',
            self::Shipping => 'info',
            self::Completed => 'success',
            self::Cancelled => 'secondary',
        };
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('order.state.'.$this->value, locale: $locale);
    }
}
