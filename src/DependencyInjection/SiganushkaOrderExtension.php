<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SiganushkaOrderExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $repositoriesMapping = [
            'order_class' => OrderRepository::class,
            'order_item_class' => OrderItemRepository::class,
            'order_adjustment' => OrderAdjustmentRepository::class,
        ];

        foreach ($repositoriesMapping as $configName => $repositoryClass) {
            $repositoryDef = $container->findDefinition($repositoryClass);
            $repositoryDef->setArgument('$entityClass', $config[$configName]);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('siganushka_generic')) {
            return;
        }

        $configs = $container->getExtensionConfig($this->getAlias());

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
    }
}
