<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Inventory\OrderInventoryModifierInterface;

class OrderInventoryListener
{
    public function __construct(private readonly OrderInventoryModifierInterface $inventoryModifier)
    {
    }

    public function __invoke(OnFlushEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $uow = $em->getUnitOfWork();

        $recomputeChangeSet = function (Order $entity) use ($em, $uow): void {
            foreach ($entity->getItems() as $item) {
                $subject = $item->getSubject();
                $subject && $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($subject::class), $subject);
            }
        };

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Order) {
                $this->inventoryModifier->modifiy(OrderInventoryModifierInterface::DECREASE, $entity);
                $recomputeChangeSet($entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Order) {
                $state = $uow->getEntityChangeSet($entity)['state'][1] ?? null;
                if ($state === OrderState::Cancelled->value) {
                    $this->inventoryModifier->modifiy(OrderInventoryModifierInterface::INCREASE, $entity);
                    $recomputeChangeSet($entity);
                }
            }
        }
    }
}
