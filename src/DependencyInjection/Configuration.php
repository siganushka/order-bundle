<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Entity\OrderAdjustment;
use Siganushka\OrderBundle\Entity\OrderItem;
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
                            ->ifTrue(function (mixed $v) use ($classFqcn) {
                                if (!class_exists($v)) {
                                    return false;
                                }

                                return !is_subclass_of($v, $classFqcn);
                            })
                            ->thenInvalid('The %s class must extends "'.$classFqcn.'" for using the "'.$configName.'".')
                        ->end()
                    ->end()
            ;
        }

        return $treeBuilder;
    }
}
