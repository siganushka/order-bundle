<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\DependencyInjection;

use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Entity\AbstractOrderAdjustment;
use Siganushka\OrderBundle\Entity\AbstractOrderItem;
use Siganushka\OrderBundle\Form\Type\OrderItemSubjectType;
use Siganushka\OrderBundle\Generator\OrderNumberGenerator;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Siganushka\OrderBundle\Stock\OrderStockModifier;
use Siganushka\OrderBundle\Stock\OrderStockModifierInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const RESOURCE_MAPPING = [
        'order_class' => [AbstractOrder::class, OrderRepository::class],
        'order_item_class' => [AbstractOrderItem::class, OrderItemRepository::class],
        'order_adjustment_class' => [AbstractOrderAdjustment::class, OrderAdjustmentRepository::class],
    ];

    /**
     * @return TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_order');
        $rootNode = $treeBuilder->getRootNode();

        foreach (self::RESOURCE_MAPPING as $configName => [$interface]) {
            $rootNode->children()
                ->scalarNode($configName)
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, $interface, true))
                        ->thenInvalid('The value must be instanceof '.$interface.', %s given.')
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
            ->stringNode('order_expire_transport')
                ->defaultNull()
            ->end()
            ->integerNode('order_expire_seconds')
                ->defaultValue(3600)
                ->validate()
                    ->ifTrue(static fn (mixed $v): bool => \is_int($v) && $v <= 0)
                    ->thenInvalid('The value must be greater than 0, %s given.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
