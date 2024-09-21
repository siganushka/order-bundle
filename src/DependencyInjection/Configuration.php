<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Entity\OrderAdjustment;
use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Generator\UniqidNumberGenerator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_order');
        /** @var ArrayNodeDefinition */
        $rootNode = $treeBuilder->getRootNode();

        $classMapping = [
            'order_class' => Order::class,
            'order_item_class' => OrderItem::class,
            'order_adjustment' => OrderAdjustment::class,
        ];

        foreach ($classMapping as $configName => $classFqcn) {
            $rootNode
                ->children()
                    ->scalarNode($configName)
                        ->defaultValue($classFqcn)
                        ->validate()
                            ->ifTrue(static fn (mixed $v): bool => !is_a($v, $classFqcn, true))
                            ->thenInvalid('The value must be instanceof '.$classFqcn.', %s given.')
                        ->end()
                    ->end()
            ;
        }

        $rootNode->children()
            ->scalarNode('order_number_generator')
                ->cannotBeEmpty()
                ->defaultValue(UniqidNumberGenerator::class)
                ->validate()
                    ->ifTrue(static fn (mixed $v): bool => !is_a($v, OrderNumberGeneratorInterface::class, true))
                    ->thenInvalid('The value must be instanceof '.OrderNumberGeneratorInterface::class.', %s given.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
