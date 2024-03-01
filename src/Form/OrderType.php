<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Form;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Entity\OrderItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Unique;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $data = $event->getData();
        $disabled = $data instanceof Order && null !== $data->getId() ? true : false;

        $form = $event->getForm();
        $form->add('items', CollectionType::class, [
            'label' => 'order.items',
            'entry_type' => OrderItemType::class,
            'entry_options' => ['label' => false],
            'disabled' => $disabled,
            'allow_add' => true,
            'allow_delete' => true,
            'error_bubbling' => false,
            'by_reference' => false,
            'constraints' => [
                new Count(['min' => 1, 'minMessage' => 'order.items.min_count.invalid']),
                new Unique(['message' => 'order_item.variant.unique', 'normalizer' => fn (OrderItem $item) => $item->getVariant() ?? spl_object_hash($item)]),
            ],
        ]);
    }
}
