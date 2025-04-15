<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderItemSubjectEntityType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /** @var ClassMetadata */
        $classMetadata = $this->entityManager->getMetadataFactory()
            ->getMetadataFor(OrderItemSubjectInterface::class);

        $resolver->setDefaults([
            'class' => $classMetadata->getName(),
            'choice_label' => fn (OrderItemSubjectInterface $subject) => $subject->getName(),
            'query_builder' => fn (GenericEntityRepository $er) => $er->createQueryBuilderWithOrdered('entity'),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
