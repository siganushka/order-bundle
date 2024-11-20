<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Exception\FormErrorException;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Event\OrderBeforeDeleteEvent;
use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Siganushka\OrderBundle\Event\OrderDeletedEvent;
use Siganushka\OrderBundle\Form\OrderType;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderController extends AbstractController
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OrderRepository $orderRepository)
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
    public function postCollection(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->orderRepository->createNew();

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new FormErrorException($form);
        }

        $event = new OrderBeforeCreateEvent($entity);
        $this->eventDispatcher->dispatch($event);

        $entityManager->beginTransaction();
        $entityManager->persist($entity);
        $entityManager->flush();
        $entityManager->commit();

        $event = new OrderCreatedEvent($entity);
        $this->eventDispatcher->dispatch($event);

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

        $event = new OrderBeforeDeleteEvent($entity);
        $this->eventDispatcher->dispatch($event);

        $entityManager->remove($entity);
        $entityManager->flush();

        $event = new OrderDeletedEvent($entity);
        $this->eventDispatcher->dispatch($event);

        // 204 No Content
        return $this->createResponse(null, Response::HTTP_NO_CONTENT);
    }

    protected function createResponse(?object $data, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        $attributes = [
            'number', 'itemsTotal', 'adjustmentsTotal', 'total', 'state',
            'items' => [
                'subject' => ['id', 'name', 'price', 'inventory'],
                'price', 'quantity', 'subtotal',
            ],
            'adjustments' => ['amount'],
            'updatedAt', 'createdAt',
        ];

        return $this->json($data, $statusCode, $headers, compact('attributes'));
    }
}
