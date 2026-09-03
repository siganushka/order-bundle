<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Doctrine\ORM\Events;
use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\OrderBundle\EventListener\OrderCheckFreeListener;
use Siganushka\OrderBundle\EventListener\OrderExpireMessageListener;
use Siganushka\OrderBundle\EventListener\OrderNumberGenerateListener;
use Siganushka\OrderBundle\EventListener\OrderStockModifierListener;
use Siganushka\OrderBundle\Form\OrderItemType;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Message\OrderExpireMessage;
use Siganushka\OrderBundle\MessageHandler\OrderExpireMessageHandler;
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

        foreach (Configuration::RESOURCE_MAPPING as $configName => [, $repositoryClass]) {
            $repositoryClass = $container->findDefinition($repositoryClass);
            $repositoryClass->setArgument('$entityClass', $config[$configName]);
        }

        $container->setParameter('siganushka_order.order_expire_transport', $config['order_expire_transport']);
        $container->setParameter('siganushka_order.order_expire_seconds', $config['order_expire_seconds']);

        $container->setAlias(OrderNumberGeneratorInterface::class, $config['order_number_generator']);
        $container->setAlias(OrderStockModifierInterface::class, $config['order_stock_modifier']);

        $container->findDefinition(OrderItemType::class)
            ->setArgument('$subjectFormType', $config['order_item_subject_type'])
        ;

        $container->findDefinition(OrderNumberGenerateListener::class)
            ->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => 8])
        ;

        $container->findDefinition(OrderCheckFreeListener::class)
            ->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -8])
        ;

        $container->findDefinition(OrderStockModifierListener::class)
            ->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['order_class'], 'priority' => -256])
        ;

        $container->findDefinition(OrderExpireMessageListener::class)
            ->addTag('doctrine.orm.entity_listener', ['event' => Events::postPersist, 'entity' => $config['order_class'], 'priority' => -256])
            ->addTag('doctrine.event_listener', ['event' => Events::postFlush])
        ;

        if (!interface_exists(MessageBusInterface::class) || !$config['order_expire_transport']) {
            $container->removeDefinition(OrderExpireMessageListener::class);
            $container->removeDefinition(OrderExpireMessageHandler::class);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = array_merge(...$configs);

        $resolveTargetEntities = [];
        foreach (Configuration::RESOURCE_MAPPING as $configName => [$interface]) {
            $resolveTargetEntities[$interface] = $config[$configName] ?? null;
        }

        if (\count($rte = array_filter($resolveTargetEntities))) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => ['resolve_target_entities' => $rte],
            ]);
        }

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
                    'supports' => AbstractOrder::class,
                    'transitions' => $transitions,
                    'marking_store' => [
                        'type' => 'method',
                        'property' => 'state',
                    ],
                ],
            ],
        ]);

        if (interface_exists(MessageBusInterface::class) && $transport = ($config['order_expire_transport'] ?? null)) {
            $container->prependExtensionConfig('framework', [
                'messenger' => [
                    'routing' => [OrderExpireMessage::class => $transport],
                ],
            ]);
        }
    }
}
