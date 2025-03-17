<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form;

use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderItemType extends AbstractType
{
    public function __construct(
        private readonly OrderItemRepository $repository,
        private readonly string $subjectFormType)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', $this->subjectFormType, [
                'label' => 'order_item.subject',
                'placeholder' => 'generic.choice',
                'constraints' => new NotBlank(),
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'order_item.quantity',
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0),
                    new LessThanOrEqual(propertyPath: 'parent.data.subject?.inventory', message: 'Insufficient quantity in stock.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->repository->getClassName(),
        ]);
    }
}
