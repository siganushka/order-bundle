<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutesTest extends TestCase
{
    protected RouteCollection $routes;

    protected function setUp(): void
    {
        $locator = new FileLocator(__DIR__.'/../config/');

        new LoaderResolver([
            $loader = new PhpFileLoader($locator),
            new AttributeDirectoryLoader($locator, new AttributeRouteControllerLoader()),
        ]);

        $this->routes = $loader->load('routes.php');
    }

    public function testAll(): void
    {
        static::assertSame([
            'siganushka_order_order_getcollection',
            'siganushka_order_order_postcollection',
            'siganushka_order_order_getitem',
            'siganushka_order_order_putitem',
            'siganushka_order_order_deleteitem',
        ], array_keys($this->routes->all()));
    }

    /**
     * @dataProvider routesProvider
     */
    public function testRotues(string $routeName, string $path, array $methods): void
    {
        /** @var Route */
        $route = $this->routes->get($routeName);

        static::assertSame($path, $route->getPath());
        static::assertSame($methods, $route->getMethods());
        static::assertTrue($route->getDefault('_stateless'));
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
