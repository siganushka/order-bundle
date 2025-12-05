<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\OrderBundle\Controller\OrderController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('siganushka_order_getcollection', '/orders')
        ->controller([OrderController::class, 'getCollection'])
        ->methods(['GET'])
    ;

    $routes->add('siganushka_order_postcollection', '/orders')
        ->controller([OrderController::class, 'postCollection'])
        ->methods(['POST'])
    ;

    $routes->add('siganushka_order_getitem', '/orders/{number<[a-zA-Z0-9]+>}')
        ->controller([OrderController::class, 'getItem'])
        ->methods(['GET'])
    ;

    $routes->add('siganushka_order_putitem', '/orders/{number<[a-zA-Z0-9]+>}')
        ->controller([OrderController::class, 'putItem'])
        ->methods(['PUT', 'PATCH'])
    ;

    $routes->add('siganushka_order_deleteitem', '/orders/{number<[a-zA-Z0-9]+>}')
        ->controller([OrderController::class, 'deleteItem'])
        ->methods(['DELETE'])
    ;
};
