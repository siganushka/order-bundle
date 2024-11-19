<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Doctrine\ORM\Events;
use Godruoyi\Snowflake\Snowflake;
use Siganushka\OrderBundle\Doctrine\OrderCancelledMessageListener;
use Siganushka\OrderBundle\Doctrine\OrderConfirmFreeListener;
use Siganushka\OrderBundle\Doctrine\OrderInventoryModifierListener;
use Siganushka\OrderBundle\Doctrine\OrderNumberGeneratorListener;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Generator\SnowflakeNumberGenerator;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;
use Siganushka\OrderBundle\MessageHandler\OrderCancelledMessageHandler;
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

        $container->setAlias(OrderNumberGeneratorInterface::class, $config['order_number_generator']);
        $container->setAlias(OrderInventoryModifierInterface::class, $config['order_inventory_modifier']);

        $orderNumberGenerateListenerDef = $container->findDefinition(OrderNumberGeneratorListener::class);
        $orderNumberGenerateListenerDef->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => 4]);

        $orderConfirmFreeListenerDef = $container->findDefinition(OrderConfirmFreeListener::class);
        $orderConfirmFreeListenerDef->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -4]);

        $orderInventoryModifierListenerDef = $container->findDefinition(OrderInventoryModifierListener::class);
        $orderInventoryModifierListenerDef->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -8]);

        $orderCancelledMessageListenerDef = $container->findDefinition(OrderCancelledMessageListener::class);
        $orderCancelledMessageListenerDef->addTag('doctrine.orm.entity_listener', ['event' => Events::preFlush, 'entity' => $config['order_class'], 'priority' => -16]);
        $orderCancelledMessageListenerDef->setArgument('$expireIn', $config['order_expire_in']);

        $orderCancelledMessageHandlerDef = $container->findDefinition(OrderCancelledMessageHandler::class);
        $orderCancelledMessageHandlerDef->addTag('messenger.message_handler');

        foreach (Configuration::$resourceMapping as $configName => [, $repositoryClass]) {
            $repositoryDef = $container->findDefinition($repositoryClass);
            $repositoryDef->setArgument('$entityClass', $config[$configName]);
        }

        if (!class_exists(Snowflake::class)) {
            $container->removeDefinition(SnowflakeNumberGenerator::class);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $mappingOverride = [];
        foreach (Configuration::$resourceMapping as $configName => [$entityClass]) {
            if ($config[$configName] !== $entityClass) {
                $mappingOverride[$entityClass] = $config[$configName];
            }
        }

        $container->prependExtensionConfig('siganushka_generic', [
            'doctrine' => ['mapping_override' => $mappingOverride],
        ]);

        $transitions = [];
        foreach (OrderStateTransition::cases() as $transition) {
            $from = array_map(fn (OrderState $item) => $item->value, $transition->froms());
            $to = array_map(fn (OrderState $item) => $item->value, $transition->tos());
            $transitions[$transition->value] = compact('from', 'to');
        }

        $container->prependExtensionConfig('framework', [
            'workflows' => [
                'order_state_flow' => [
                    'supports' => Order::class,
                    'transitions' => $transitions,
                    'marking_store' => [
                        'type' => 'method',
                        'property' => 'stateAsString',
                    ],
                ],
            ],
        ]);
    }
}
