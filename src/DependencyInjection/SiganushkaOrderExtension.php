<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Doctrine\ORM\Events;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\EventListener\OrderCancelMessageListener;
use Siganushka\OrderBundle\EventListener\OrderCheckFreeListener;
use Siganushka\OrderBundle\EventListener\OrderNumberGenerateListener;
use Siganushka\OrderBundle\EventListener\OrderStockModifierListener;
use Siganushka\OrderBundle\Form\OrderItemType;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Message\OrderCancelMessage;
use Siganushka\OrderBundle\MessageHandler\OrderCancelMessageHandler;
use Siganushka\OrderBundle\Stock\OrderStockModifierInterface;
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

        $container->setParameter('siganushka_order.order_cancel_transport', $config['order_cancel_transport']);
        $container->setParameter('siganushka_order.order_cancel_seconds', $config['order_cancel_seconds']);

        $container->setAlias(OrderNumberGeneratorInterface::class, $config['order_number_generator']);
        $container->setAlias(OrderStockModifierInterface::class, $config['order_stock_modifier']);

        $orderItemType = $container->findDefinition(OrderItemType::class);
        $orderItemType->setArgument('$subjectFormType', $config['order_item_subject_type']);

        $orderNumberGenerateListener = $container->findDefinition(OrderNumberGenerateListener::class);
        $orderNumberGenerateListener->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => 8]);

        $orderCheckFreeListener = $container->findDefinition(OrderCheckFreeListener::class);
        $orderCheckFreeListener->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -8]);

        $orderStockModifierListener = $container->findDefinition(OrderStockModifierListener::class);
        $orderStockModifierListener->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -256]);

        $orderCancelMessageListener = $container->findDefinition(OrderCancelMessageListener::class);
        $orderCancelMessageListener->setArgument('$seconds', $config['order_cancel_seconds']);
        $orderCancelMessageListener->addTag('doctrine.orm.entity_listener', ['event' => Events::postPersist, 'entity' => $config['order_class'], 'priority' => -256]);

        if (!interface_exists(MessageBusInterface::class) || !$config['order_cancel_transport']) {
            $container->removeDefinition(OrderCancelMessageListener::class);
            $container->removeDefinition(OrderCancelMessageHandler::class);
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

        $container->prependExtensionConfig('doctrine', [
            'orm' => ['resolve_target_entities' => $mappingOverride],
        ]);

        $container->prependExtensionConfig('siganushka_generic', [
            'doctrine' => ['mapping_override' => $mappingOverride],
        ]);

        $transitions = [];
        foreach (OrderStateTransition::cases() as $transition) {
            $transitions[$transition->value] = [
                'from' => $transition->froms(),
                'to' => $transition->tos(),
            ];
        }

        $container->prependExtensionConfig('framework', [
            'workflows' => [
                'order' => [
                    'supports' => Order::class,
                    'transitions' => $transitions,
                    'marking_store' => [
                        'type' => 'method',
                        'property' => 'state',
                    ],
                ],
            ],
        ]);

        if (interface_exists(MessageBusInterface::class) && $config['order_cancel_transport']) {
            $container->prependExtensionConfig('framework', [
                'messenger' => [
                    'routing' => [
                        OrderCancelMessage::class => $config['order_cancel_transport'],
                    ],
                ],
            ]);
        }
    }
}
