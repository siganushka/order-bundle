<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\OrderBundle\Form\Type\OrderItemSubjectType;
use Siganushka\OrderBundle\Generator\OrderNumberGenerator;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Siganushka\OrderBundle\Stock\OrderStockModifier;
use Siganushka\OrderBundle\Stock\OrderStockModifierInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public static array $resourceMapping = [
        'order_class' => [Order::class, OrderRepository::class],
        'order_item_class' => [OrderItem::class, OrderItemRepository::class],
    ];

    /**
     * @return TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_order');
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
                ->defaultValue(OrderNumberGenerator::class)
                ->validate()
                    ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, OrderNumberGeneratorInterface::class, true))
                    ->thenInvalid('The value must be instanceof '.OrderNumberGeneratorInterface::class.', %s given.')
                ->end()
            ->end()
            ->scalarNode('order_stock_modifier')
                ->cannotBeEmpty()
                ->defaultValue(OrderStockModifier::class)
                ->validate()
                    ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, OrderStockModifierInterface::class, true))
                    ->thenInvalid('The value must be instanceof '.OrderStockModifierInterface::class.', %s given.')
                ->end()
            ->end()
            ->scalarNode('order_item_subject_type')
                ->example('You can using symfony/ux-autocomplete (e.g: App\Form\FoodAutocompleteField)')
                ->defaultValue(OrderItemSubjectType::class)
            ->end()
            ->integerNode('order_cancelled_expires')
                ->defaultValue(1800)
            ->end()
        ;

        return $treeBuilder;
    }
}
