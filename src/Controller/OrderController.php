<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\OrderBundle\Dto\OrderFilterDto;
use Siganushka\OrderBundle\Form\OrderType;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    #[Route('/orders', methods: 'GET')]
    public function getCollection(PaginatorInterface $paginator, #[MapQueryString] OrderFilterDto $dto): Response
    {
        $queryBuilder = $this->orderRepository->createQueryBuilderWithFilter('o', $dto);
        $pagination = $paginator->paginate($queryBuilder);

        return $this->createResponse($pagination);
    }

    #[Route('/orders', methods: 'POST')]
    public function postCollection(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->orderRepository->createNew();

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->beginTransaction();
        $entityManager->persist($entity);
        $entityManager->flush();
        $entityManager->commit();

        return $this->createResponse($entity, Response::HTTP_CREATED);
    }

    #[Route('/orders/{number}', methods: 'GET')]
    public function getItem(string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number)
            ?? throw $this->createNotFoundException();

        return $this->createResponse($entity);
    }

    #[Route('/orders/{number}', methods: ['PUT', 'PATCH'])]
    public function putItem(Request $request, EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->request->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();

        return $this->createResponse($entity);
    }

    #[Route('/orders/{number}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number)
            ?? throw $this->createNotFoundException();

        $entityManager->remove($entity);
        $entityManager->flush();

        // 204 No Content
        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    protected function createResponse(mixed $data, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        $attributes = [
            'number', 'itemsTotal', 'adjustmentsTotal', 'total', 'state',
            'items' => [
                'subject' => ['id', 'name', 'price', 'inventory'],
                'price',
                'quantity',
                'subtotal',
            ],
            'adjustments' => ['type', 'label', 'amount'],
            'updatedAt',
            'createdAt',
        ];

        return $this->json($data, $statusCode, $headers, compact('attributes'));
    }
}
