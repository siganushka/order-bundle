<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form;

use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\ProductBundle\Entity\ProductVariant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
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
                'choice_label' => function (ProductVariant $variant) {
                    $optionValues = $variant->getOptionValues();
                    if ($optionValues->isEmpty()) {
                        return $variant->getProduct()->getName();
                    }

                    return sprintf('%s【%s】', $variant->getProduct()->getName(), $optionValues->getLabel());
                },
                'choice_attr' => fn (ProductVariant $variant): array => ['disabled' => $variant->isOutOfStock()],
                'constraints' => new NotBlank(),
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'order_item.quantity',
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(0),
                    new LessThanOrEqual(2147483647),
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
}
