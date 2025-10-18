<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form;

use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\OrderBundle\Model\StockableInterface;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
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
    public function __construct(
        private readonly OrderItemRepository $repository,
        private readonly string $subjectFormType)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', $this->subjectFormType, [
                // Attributes when embedded in a collection
                'label' => false === $options['label'] ? null : 'order_item.subject',
                'constraints' => new NotBlank(),
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'order_item.quantity',
                // Attributes when embedded in a collection
                'row_attr' => false === $options['label'] ? ['class' => 'col-3'] : [],
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
            'data_class' => $this->repository->getClassName(),
            'constraints' => new Callback($this->validateQuantity(...)),
        ]);
    }

    private function validateQuantity(OrderItem $object, ExecutionContextInterface $context): void
    {
        $subject = $object->getSubject();
        if (!$subject instanceof StockableInterface) {
            return;
        }

        $stock = $subject->getAvailableStock();
        $quantity = $object->getQuantity();
        if (\is_int($stock) && \is_int($quantity) && $stock < $quantity) {
            $context->buildViolation('Out of Stock.')
                ->setParameter('{{ stock }}', (string) $stock)
                ->setParameter('{{ quantity }}', (string) $quantity)
                ->atPath('quantity')
                ->addViolation()
            ;
        }
    }
}
