<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\OrderBundle\Controller\OrderController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('siganushka_order_getcollection', '/orders')
        ->controller([OrderController::class, 'getCollection'])
        ->methods(['GET'])
        ->stateless(true)
    ;

    $routes->add('siganushka_order_postcollection', '/orders')
        ->controller([OrderController::class, 'postCollection'])
        ->methods(['POST'])
        ->stateless(true)
    ;

    $routes->add('siganushka_order_getitem', '/orders/{number}')
        ->controller([OrderController::class, 'getItem'])
        ->methods(['GET'])
        ->stateless(true)
    ;

    $routes->add('siganushka_order_putitem', '/orders/{number}')
        ->controller([OrderController::class, 'putItem'])
        ->methods(['PUT', 'PATCH'])
        ->stateless(true)
    ;

    $routes->add('siganushka_order_deleteitem', '/orders/{number}')
        ->controller([OrderController::class, 'deleteItem'])
        ->methods(['DELETE'])
        ->stateless(true)
    ;
};
