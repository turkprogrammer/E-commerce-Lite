<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Port\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API контроллер для товаров
 */
#[AsController]
class ProductController extends AbstractApiController
{
    public function __construct(
        private ProductRepositoryInterface $productRepo,
        protected SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    /**
     * Получить список всех товаров
     */
    #[Route('/api/products', name: 'api_products_list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $products = $this->productRepo->findAll();

        return $this->success([
            'products' => $products,
            'total' => count($products),
        ], 'Товары получены', Response::HTTP_OK);
    }

    /**
     * Получить избранные товары
     */
    #[Route('/api/products/featured', name: 'api_products_featured', methods: ['GET'])]
    public function featured(Request $request): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 10);
        $products = $this->productRepo->findFeatured($limit);

        return $this->success([
            'products' => $products,
            'total' => count($products),
        ], 'Избранные товары получены', Response::HTTP_OK);
    }

    /**
     * Получить товар по ID
     */
    #[Route('/api/products/{id}', name: 'api_product_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepo->findById($id);

        if (!$product) {
            return $this->error('Товар не найден', Response::HTTP_NOT_FOUND);
        }

        if (!$product->isActive()) {
            return $this->error('Товар не активен', Response::HTTP_NOT_FOUND);
        }

        return $this->success([
            'product' => $product,
        ], 'Товар найден', Response::HTTP_OK);
    }
}
