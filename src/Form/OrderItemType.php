<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form;

use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\ProductBundle\Entity\ProductVariant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('variant', EntityType::class, [
                'label' => 'order_item.variant',
                'class' => ProductVariant::class,
                'placeholder' => 'generic.choice',
                'choice_value' => 'id',
                'choice_label' => 'id',
                // 'choice_label' => ChoiceList::label($this, [__CLASS__, 'createChoiceLabel']),
                'choice_attr' => fn (ProductVariant $variant): array => ['disabled' => $variant->isOutOfStock()],
                'constraints' => new NotBlank(),
                'block_prefix' => 'sigan_order_item_variant',
                'attr' => ['class' => 'tom-select', 'placeholder' => 'order_item.variant.placeholder'],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'order_item.quantity',
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }

    public static function createChoiceLabel(ProductVariant $variant): ?string
    {
        $product = $variant->getProduct();
        $label = $variant->getChoiceLabel();

        if (null === $product) {
            return $label;
        }

        $productName = $product->getName();
        if (\is_string($productName) && \is_string($label)) {
            return sprintf('%s【%s】', $productName, $label);
        }

        return $productName;
    }
}
