<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{

    public const PRODUCTS = [
        [
            "id"    => 1,
            "name"  => "product 1",
            "price" => 100.00
        ],
        [
            "id"    => 2,
            "name"  => "product 2",
            "price" => 200.00
        ],
        [
            "id"    => 3,
            "name"  => "product 3",
            "price" => 300.00
        ],
        [
            "id"    => 4,
            "name"  => "product 4",
            "price" => 400.00
        ]
    ];

    #[Route('/products', name: 'app_get_products', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        return $this->json(self::PRODUCTS);
    }

    #[Route('/products/{id}', name: 'app_get_products_by_id', methods: ['GET'])]
    public function getProductById(string $id): JsonResponse
    {
        $productsData = self::PRODUCTS;

        foreach ($productsData as $product) {
            if ($product['id'] == $id) {
                return $this->json($product);
            }
        }

        return $this->json("Product not found", Response::HTTP_NOT_FOUND);
    }

    #[Route('/products', name: 'app_create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $newProduct = null;

        $newProductId = rand(5, 100);

        $newProduct['id'] = $newProductId;
        $newProduct['name'] = $requestData['name'];
        $newProduct['price'] = $requestData['price'];

        return $this->json($newProduct, Response::HTTP_CREATED);
    }

    #[Route('/products/{id}', name: 'app_update_product_by_id', methods: ['PATCH'])]
    public function updateProduct(string $id, Request $request): JsonResponse
    {
        $productsData = self::PRODUCTS;

        $neededProduct = null;

        foreach ($productsData as $product) {
            if ($product['id'] == $id) {
                $neededProduct = $product;
            }
        }

        if (empty($neededProduct)) {
            return $this->json("Product not found", Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);

        $neededProduct['name'] = $requestData['name'];
        $neededProduct['price'] = $requestData['price'];

        return $this->json($neededProduct, Response::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'app_delete_product_by_id', methods: ['DELETE'])]
    public function deleteProductById(string $id): JsonResponse
    {
        $productsData = self::PRODUCTS;

        foreach ($productsData as $product) {
            if ($product['id'] == $id) {
                return $this->json(null, Response::HTTP_NO_CONTENT);
            }
        }

        return $this->json("Product not found", Response::HTTP_NOT_FOUND);
    }

}
