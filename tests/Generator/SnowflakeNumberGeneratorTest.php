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

    // public function testPerformance(): void
    // {
    //     $generator = new SnowflakeNumberGenerator();

    //     $numbers = [];
    //     $count = 1000000;

    //     $preTime = microtime(true);
    //     for ($i = 0; $i < $count; ++$i) {
    //         $number = $generator->generate(new Order());
    //         $numbers[$number] = 1;

    //         // if ($i < 10) var_dump($number);
    //     }

    //     $postTime = microtime(true);
    //     $execTime = $postTime - $preTime;

    //     echo \sprintf('共计生成 %d 条记录，重复 %d 条，共耗时 %f'.\PHP_EOL, $count, $count - \count($numbers), $execTime);
    //     // static::assertCount($count, $numbers);
    // }
}
