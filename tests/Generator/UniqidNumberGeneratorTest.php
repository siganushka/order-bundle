<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Generator\UniqidNumberGenerator;

class UniqidNumberGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $generator = new UniqidNumberGenerator();
        $number = $generator->generate(new Order());

        static::assertNotEmpty($number);
        static::assertSame(16, mb_strlen($number));
    }

    public function testPerformance(): void
    {
        $generator = new UniqidNumberGenerator();

        $numbers = [];
        $count = 100000;

        $preTime = microtime(true);
        for ($i = 0; $i < $count; ++$i) {
            $number = $generator->generate(new Order());
            $numbers[$number] = 1;

            // if ($i < 10) var_dump($number);
        }

        $postTime = microtime(true);
        $execTime = $postTime - $preTime;

        echo \sprintf('共计生成 %d 条记录，重复 %d 条，共耗时 %f', $count, $count - \count($numbers), $execTime);
        static::assertCount($count, $numbers);
    }
}
