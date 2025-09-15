<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Siganushka\Contracts\Doctrine\EnableInterface;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderItemSubjectType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /** @var ClassMetadata */
        $classMetadata = $this->entityManager->getMetadataFactory()
            ->getMetadataFor(OrderItemSubjectInterface::class);

        $subjectClass = $classMetadata->getName();
        $queryBuilder = fn (GenericEntityRepository $er) => is_subclass_of($subjectClass, EnableInterface::class)
                ? $er->createQueryBuilderWithOrdered('entity')->andWhere('entity.enabled = :enabled')->setParameter('enabled', true)
                : $er->createQueryBuilderWithOrdered('entity');

        $resolver->setDefaults([
            'class' => $classMetadata->getName(),
            'choice_label' => fn (OrderItemSubjectInterface $subject) => $subject->getName(),
            'query_builder' => $queryBuilder,
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
