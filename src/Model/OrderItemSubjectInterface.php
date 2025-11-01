<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

use Siganushka\Contracts\Doctrine\ResourceInterface;

interface OrderItemSubjectInterface extends ResourceInterface
{
    public function getSubjectTitle(): string;

    public function getSubjectPrice(): int;

    public function getSubjectExtra(): ?string;

    public function getSubjectImg(): ?string;
}
