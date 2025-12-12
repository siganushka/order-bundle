<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\OrderBundle\Dto\OrderQueryDto;
use Siganushka\OrderBundle\Form\OrderType;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

class OrderController extends AbstractController
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function getCollection(PaginatorInterface $paginator, #[MapQueryString] OrderQueryDto $dto): Response
    {
        $queryBuilder = $this->orderRepository->createQueryBuilderByDto('o', $dto);
        $pagination = $paginator->paginate($queryBuilder, $dto->page, $dto->size);

        return $this->json($pagination, context: [
            'groups' => ['collection'],
        ]);
    }

    public function postCollection(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->orderRepository->createNew();

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->getPayload()->all());

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->beginTransaction();
        $entityManager->persist($entity);
        $entityManager->flush();
        $entityManager->commit();

        return $this->json($entity, Response::HTTP_CREATED, context: [
            'groups' => ['item'],
        ]);
    }

    public function getItem(string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number)
            ?? throw $this->createNotFoundException();

        return $this->json($entity, context: [
            'groups' => ['item'],
        ]);
    }

    public function putItem(Request $request, EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(OrderType::class, $entity);
        $form->submit($request->getPayload()->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();

        return $this->json($entity, context: [
            'groups' => ['item'],
        ]);
    }

    public function deleteItem(EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number)
            ?? throw $this->createNotFoundException();

        $entityManager->remove($entity);
        $entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
