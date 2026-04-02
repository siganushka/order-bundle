<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Generator;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Generator\RedisNumberGenerator;

class RedisNumberGeneratorTest extends AbstractGeneratorTestCase
{
    public function testGenerate(): void
    {
        $generator = $this->getGenerator();
        $number = $generator->generate(new Order());

        static::assertNotEmpty($number);
        static::assertSame(12, mb_strlen($number));
    }

    protected function getGenerator(): OrderNumberGeneratorInterface
    {
        return new RedisNumberGenerator();
    }
}
