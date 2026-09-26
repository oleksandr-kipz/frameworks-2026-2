<?php

namespace App\Controller;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/products', name: 'app_get_products', methods: ['GET'])]
    public function getProducts(Request $request): JsonResponse
    {
        $queryParams = $request->query->all();

        $page = 1;
        $itemsPerPage = 5;

        if (isset($queryParams['page'])) {
            $page = $queryParams['page'];
        }

        if (isset($queryParams['itemsPerPage'])) {
            $itemsPerPage = $queryParams['itemsPerPage'];
        }

        $products = $this->entityManager->getRepository(Products::class)->findAllByParams($page, $itemsPerPage, $queryParams);

        return $this->json($products);
    }

    #[Route('/products/{id}', name: 'app_get_products_by_id', methods: ['GET'])]
    public function getProductById(string $id): JsonResponse
    {
        /** @var Products $product */
        $product = $this->entityManager->getRepository(Products::class)->findOneBy(['id' => $id]);

        if (!$product) {
            return $this->json("Product not found", Response::HTTP_NOT_FOUND);
        }

        return $this->json($product, Response::HTTP_OK);
    }

    #[Route('/products', name: 'app_create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $newProduct = new Products();

        $newProduct->setName($requestData['name']);
        $newProduct->setDescription($requestData['description']);
        $newProduct->setPrice($requestData['price']);

        $this->entityManager->persist($newProduct);
        $this->entityManager->flush();

        return $this->json($newProduct, Response::HTTP_CREATED);
    }

    #[Route('/products/{id}', name: 'app_update_product_by_id', methods: ['PATCH'])]
    public function updateProduct(string $id, Request $request): JsonResponse
    {
        /** @var Products $product */
        $product = $this->entityManager->getRepository(Products::class)->findOneBy(['id' => $id]);

        if (!$product) {
            return $this->json("Product not found", Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);

        if (isset($requestData['name'])) {
            $product->setName($requestData['name']);
        }

        if (isset($requestData['description'])) {
            $product->setDescription($requestData['description']);
        }

        if (isset($requestData['price'])) {
            $product->setPrice($requestData['price']);
        }

        $this->entityManager->flush();

        return $this->json($product, Response::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'app_delete_product_by_id', methods: ['DELETE'])]
    public function deleteProductById(string $id): JsonResponse
    {
        /** @var Products $product */
        $product = $this->entityManager->getRepository(Products::class)->findOneBy(['id' => $id]);

        if (!$product) {
            return $this->json("Product not found", Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json("Success", Response::HTTP_NO_CONTENT);
    }

}
