<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaOrderBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
