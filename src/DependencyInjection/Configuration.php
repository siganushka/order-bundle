<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Entity\OrderAdjustment;
use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Generator\UniqidNumberGenerator;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;
use Siganushka\OrderBundle\Inventory\PessimisticLockInventoryModifier;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public static array $resourceMapping = [
        'order_class' => [Order::class, OrderRepository::class],
        'order_item_class' => [OrderItem::class, OrderItemRepository::class],
        'order_adjustment_class' => [OrderAdjustment::class, OrderAdjustmentRepository::class],
    ];

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_order');
        /** @var ArrayNodeDefinition */
        $rootNode = $treeBuilder->getRootNode();

        foreach (static::$resourceMapping as $configName => [$entityClass]) {
            $rootNode->children()
                ->scalarNode($configName)
                    ->defaultValue($entityClass)
                    ->validate()
                        ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, $entityClass, true))
                        ->thenInvalid('The value must be instanceof '.$entityClass.', %s given.')
                    ->end()
                ->end()
            ;
        }

        $rootNode->children()
            ->scalarNode('order_number_generator')
                ->cannotBeEmpty()
                ->defaultValue(UniqidNumberGenerator::class)
                ->validate()
                    ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, OrderNumberGeneratorInterface::class, true))
                    ->thenInvalid('The value must be instanceof '.OrderNumberGeneratorInterface::class.', %s given.')
                ->end()
            ->end()
        ;

        $rootNode->children()
            ->scalarNode('order_inventory_modifier')
                ->cannotBeEmpty()
                ->defaultValue(PessimisticLockInventoryModifier::class)
                ->validate()
                    ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, OrderInventoryModifierInterface::class, true))
                    ->thenInvalid('The value must be instanceof '.OrderInventoryModifierInterface::class.', %s given.')
                ->end()
            ->end()
        ;

        $rootNode->children()
            ->integerNode('order_expire_in')
                ->defaultValue(1800)
            ->end()
        ;

        return $treeBuilder;
    }
}
