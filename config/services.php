<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\OrderBundle\SiganushkaOrderBundle;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $ref = new \ReflectionClass(SiganushkaOrderBundle::class);
    $services->load($ref->getNamespaceName().'\\', '../src/')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/Entity/',
            '../src/Event/',
            '../src/Exception/',
            '../src/Message/',
            '../src/SiganushkaOrderBundle.php',
        ]);
};
