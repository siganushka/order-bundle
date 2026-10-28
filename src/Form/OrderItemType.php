<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form;

use Siganushka\OrderBundle\Entity\AbstractOrderItem;
use Siganushka\OrderBundle\Model\LimitableInterface;
use Siganushka\OrderBundle\Model\StockableInterface;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class OrderItemType extends AbstractType
{
    /**
     * @param class-string<FormTypeInterface> $subjectFormType
     */
    public function __construct(
        private readonly OrderItemRepository $repository,
        private readonly string $subjectFormType)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', $this->subjectFormType, [
                'label' => false === $options['label'] ? null : 'order_item.subject',
                'constraints' => new NotBlank(),
                'priority' => 0,
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'order_item.quantity',
                'row_attr' => false === $options['label'] ? ['class' => 'w-25'] : [],
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0),
                ],
                'priority' => -10,
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

    private function validateQuantity(?AbstractOrderItem $object, ExecutionContextInterface $context): void
    {
        $subject = $object?->getSubject();
        if ($subject instanceof StockableInterface
            && \is_int($stock = $subject->availableStock())
            && $stock < $object->getQuantity()) {
            $context->buildViolation('Out of Stock.')
                ->setParameter('{{ stock }}', (string) $stock)
                ->setParameter('{{ quantity }}', (string) $object->getQuantity())
                ->atPath('quantity')
                ->addViolation()
            ;
        }

        if ($subject instanceof LimitableInterface) {
            if (\is_int($min = $subject->getMinQuantity()) && $min > $object->getQuantity()) {
                $context->buildViolation('This value should be greater than or equal to {{ min }}.')
                    ->setParameter('{{ min }}', (string) $min)
                    ->setParameter('{{ quantity }}', (string) $object->getQuantity())
                    ->atPath('quantity')
                    ->addViolation()
                ;
            }

            if (\is_int($max = $subject->getMaxQuantity()) && $max < $object->getQuantity()) {
                $context->buildViolation('This value should be less than or equal to {{ max }}.')
                    ->setParameter('{{ max }}', (string) $max)
                    ->setParameter('{{ quantity }}', (string) $object->getQuantity())
                    ->atPath('quantity')
                    ->addViolation()
                ;
            }
        }
    }
}
