<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form;

use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\ProductBundle\Form\Type\ProductVariantAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class OrderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', ProductVariantAutocompleteType::class, [
                'label' => 'order_item.subject',
                'constraints' => new NotBlank(),
                'attr' => ['data-collection-class' => 'col-8'],
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
            'constraints' => new Callback([$this, 'validateQuantity']),
        ]);
    }

    public function validateQuantity(?OrderItem $object, ExecutionContextInterface $context): void
    {
        $subject = $object?->getSubject();
        $quantity = $object?->getQuantity();
        if (null === $subject || null === $quantity) {
            return;
        }

        $inventory = $subject->getInventory();
        if (null === $inventory || $inventory >= $quantity) {
            return;
        }

        $context->buildViolation('order_item.quantity_insufficient')
            ->setParameter('%inventory%', (string) $inventory)
            ->setParameter('%quantity%', (string) $quantity)
            ->atPath('quantity')
            ->addViolation()
        ;
    }
}
