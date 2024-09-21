<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;

class RoutesTest extends TestCase
{
    /**
     * @dataProvider routesProvider
     */
    public function testRotues(string $routeName, string $path, array $methods): void
    {
        $locator = new FileLocator(__DIR__.'/../../config/');

        new LoaderResolver([
            $loader = new PhpFileLoader($locator),
            new AttributeDirectoryLoader($locator, new AttributeRouteControllerLoader()),
        ]);

        $routes = $loader->load('routes.php');

        static::assertSame($path, $routes->get($routeName)?->getPath());
        static::assertSame($methods, $routes->get($routeName)?->getMethods());
    }

    public static function routesProvider(): iterable
    {
        yield ['siganushka_order_order_getcollection', '/orders', ['GET']];
        yield ['siganushka_order_order_postcollection', '/orders', ['POST']];
        yield ['siganushka_order_order_getitem', '/orders/{number}', ['GET']];
        yield ['siganushka_order_order_putitem', '/orders/{number}', ['PUT', 'PATCH']];
        yield ['siganushka_order_order_deleteitem', '/orders/{number}', ['DELETE']];
    }
}
