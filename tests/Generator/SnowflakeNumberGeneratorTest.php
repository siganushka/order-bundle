<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Generator\SnowflakeNumberGenerator;

class SnowflakeNumberGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $generator = new SnowflakeNumberGenerator();
        $number = $generator->generate(new Order());

        static::assertNotEmpty($number);
        static::assertSame(18, mb_strlen($number));
    }
}
