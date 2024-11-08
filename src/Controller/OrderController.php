<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Exception\FormErrorException;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Siganushka\OrderBundle\Form\OrderType;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class OrderController extends AbstractController
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    #[Route('/orders', methods: 'GET')]
    public function getCollection(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->createResponse($pagination);
    }

    #[Route('/orders', methods: 'POST')]
    public function postCollection(Request $request, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->orderRepository->createNew();

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new FormErrorException($form);
        }

        $entityManager->beginTransaction();

        $event = new OrderBeforeCreateEvent($entity);
        $eventDispatcher->dispatch($event);

        $entityManager->persist($entity);
        $entityManager->flush();

        $event = new OrderCreatedEvent($entity);
        $eventDispatcher->dispatch($event);

        $entityManager->commit();

        return $this->createResponse($entity, Response::HTTP_CREATED);
    }

    #[Route('/orders/{number}', methods: 'GET')]
    public function getItem(string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%s not found.', $number));
        }

        return $this->createResponse($entity);
    }

    #[Route('/orders/{number}', methods: ['PUT', 'PATCH'])]
    public function putItem(Request $request, EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%s not found.', $number));
        }

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->request->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            throw new FormErrorException($form);
        }

        $entityManager->flush();

        return $this->createResponse($entity);
    }

    #[Route('/orders/{number}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%s not found.', $number));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        // 204 No Content
        return $this->createResponse(null, Response::HTTP_NO_CONTENT);
    }

    protected function createResponse(PaginationInterface|Order|null $data, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        $attributes = [
            'number', 'itemsTotal', 'adjustmentsTotal', 'total',
            'items' => [
                'subject' => [
                    'id', 'price', 'inventory', 'img', 'choiceValue', 'choiceLabel', 'outOfStock',
                    'product' => ['id', 'name', 'img'],
                ],
                'unitPrice', 'quantity', 'subtotal',
            ],
            'adjustments' => ['amount'],
            'updatedAt', 'createdAt',
        ];

        return $this->json($data, $statusCode, $headers, compact('attributes'));
    }
}
