<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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
        $entityClass = $this->entityManager->getMetadataFactory()
            ->getMetadataFor(OrderItemSubjectInterface::class)
            ->getName();

        $queryBuilder = static function (GenericEntityRepository $er) use ($entityClass): QueryBuilder {
            $qb = $er->createQueryBuilderWithOrderBy('entity');
            if (is_subclass_of($entityClass, EnableInterface::class)) {
                $qb->andWhere('entity.enabled = :enabled')->setParameter('enabled', true);
            }

            return $qb;
        };

        $resolver->setDefaults([
            'class' => $entityClass,
            'query_builder' => $queryBuilder,
            'choice_lazy' => true,
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
