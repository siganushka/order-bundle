<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\OrderBundle\Controller\OrderController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutesTest extends TestCase
{
    protected RouteCollection $routes;

    protected function setUp(): void
    {
        $loader = new PhpFileLoader(new FileLocator(__DIR__.'/../config/'));
        $this->routes = $loader->load('routes.php');
    }

    public function testAll(): void
    {
        $routes = iterator_to_array(self::routesProvider());
        $routeNames = array_map(fn (array $route) => $route[0], $routes);

        static::assertSame($routeNames, array_keys($this->routes->all()));
    }

    /**
     * @dataProvider routesProvider
     */
    public function testRotues(string $routeName, string $path, array $methods, array $controller): void
    {
        /** @var Route */
        $route = $this->routes->get($routeName);

        static::assertSame($path, $route->getPath());
        static::assertSame($methods, $route->getMethods());
        static::assertSame($controller, $route->getDefault('_controller'));
        static::assertTrue($route->getDefault('_stateless'));
    }

    public static function routesProvider(): iterable
    {
        yield ['siganushka_order_getcollection', '/orders', ['GET'], [OrderController::class, 'getCollection']];
        yield ['siganushka_order_postcollection', '/orders', ['POST'], [OrderController::class, 'postCollection']];
        yield ['siganushka_order_getitem', '/orders/{number}', ['GET'], [OrderController::class, 'getItem']];
        yield ['siganushka_order_putitem', '/orders/{number}', ['PUT', 'PATCH'], [OrderController::class, 'putItem']];
        yield ['siganushka_order_deleteitem', '/orders/{number}', ['DELETE'], [OrderController::class, 'deleteItem']];
    }
}
