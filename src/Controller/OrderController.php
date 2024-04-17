<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Siganushka\OrderBundle\Form\OrderType;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderController extends AbstractController
{
    protected SerializerInterface $serializer;
    protected OrderRepository $orderRepository;

    public function __construct(SerializerInterface $serializer, OrderRepository $orderRepository)
    {
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/orders", methods={"GET"})
     */
    public function getCollection(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->createResponse($pagination);
    }

    /**
     * @Route("/orders", methods={"POST"})
     */
    public function postCollection(Request $request, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->orderRepository->createNew();

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->createResponse($form);
        }

        $event = new OrderBeforeCreateEvent($entity);
        $eventDispatcher->dispatch($event);

        if (null === $entity->getNumber()) {
            throw new BadRequestHttpException('Unable to generate order number.');
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        $event = new OrderCreatedEvent($entity);
        $eventDispatcher->dispatch($event);

        return $this->createResponse($entity, Response::HTTP_CREATED);
    }

    /**
     * @Route("/orders/{number<\d{16}>}", methods={"GET"})
     */
    public function getItem(string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $number));
        }

        return $this->createResponse($entity);
    }

    /**
     * @Route("/orders/{number<\d{16}>}", methods={"PUT", "PATCH"})
     */
    public function putItem(Request $request, EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $number));
        }

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->request->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return $this->createResponse($form);
        }

        $entityManager->flush();

        return $this->createResponse($entity);
    }

    /**
     * @Route("/orders/{number<\d{16}>}", methods={"DELETE"})
     */
    public function deleteItem(EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $number));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        return $this->createResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param mixed $data
     */
    protected function createResponse($data = null, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        $attributes = [
            'number', 'itemsTotal', 'adjustmentsTotal', 'total',
            'items' => [
                'variant' => [
                    'id', 'price', 'inventory', 'img', 'choiceValue', 'choiceLabel', 'outOfStock',
                    'product' => ['name', 'img'],
                ],
                'unitPrice', 'quantity', 'subtotal',
            ],
            'adjustments' => ['amount'],
            'updatedAt', 'createdAt',
        ];

        $json = $this->serializer->serialize($data, 'json', compact('attributes'));

        return JsonResponse::fromJsonString($json, $statusCode, $headers);
    }
}
