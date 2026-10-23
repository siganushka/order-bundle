<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Exception\OutOfStockException;
use Siganushka\OrderBundle\Tests\Fixtures\StockableSubject;

class OutOfStockExceptionTest extends TestCase
{
    public function testAll(): void
    {
        $subject = new StockableSubject(id: 1024, title: 'foo', price: 1, subtitle: 'bar', img: 'baz');

        $exception = new OutOfStockException($subject, 3);
        static::assertSame('Resource #1024 Out of Stock.', $exception->getMessage());
    }
}
