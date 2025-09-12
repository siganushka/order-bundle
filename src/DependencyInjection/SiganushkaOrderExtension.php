<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Doctrine\ORM\Events;
use Godruoyi\Snowflake\Snowflake;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\EventListener\OrderCheckFreeListener;
use Siganushka\OrderBundle\EventListener\OrderExpireMessageListener;
use Siganushka\OrderBundle\EventListener\OrderInventoryModifierListener;
use Siganushka\OrderBundle\EventListener\OrderNumberGenerateListener;
use Siganushka\OrderBundle\Form\OrderItemType;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Generator\SnowflakeNumberGenerator;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;
use Siganushka\OrderBundle\MessageHandler\OrderExpireMessageHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Messenger\MessageBusInterface;

class SiganushkaOrderExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach (Configuration::$resourceMapping as $configName => [, $repositoryClass]) {
            $repository = $container->findDefinition($repositoryClass);
            $repository->setArgument('$entityClass', $config[$configName]);
        }

        $container->setAlias(OrderNumberGeneratorInterface::class, $config['order_number_generator']);
        $container->setAlias(OrderInventoryModifierInterface::class, $config['order_inventory_modifier']);

        $orderItemType = $container->findDefinition(OrderItemType::class);
        $orderItemType->setArgument('$subjectFormType', $config['order_item_subject_type']);

        $orderNumberGenerateListener = $container->findDefinition(OrderNumberGenerateListener::class);
        $orderNumberGenerateListener->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => 8]);

        $orderCheckFreeListener = $container->findDefinition(OrderCheckFreeListener::class);
        $orderCheckFreeListener->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -8]);

        $orderInventoryModifierListener = $container->findDefinition(OrderInventoryModifierListener::class);
        $orderInventoryModifierListener->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -256]);

        $orderExpireMessageListener = $container->findDefinition(OrderExpireMessageListener::class);
        $orderExpireMessageListener->setArgument('$expires', $config['order_cancelled_expires']);
        $orderExpireMessageListener->addTag('doctrine.orm.entity_listener', ['event' => Events::postPersist, 'entity' => $config['order_class']]);

        $orderExpireMessageHandler = $container->findDefinition(OrderExpireMessageHandler::class);
        $orderExpireMessageHandler->addTag('messenger.message_handler');

        if (!interface_exists(MessageBusInterface::class)) {
            $container->removeDefinition(OrderExpireMessageListener::class);
            $container->removeDefinition(OrderExpireMessageHandler::class);
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
