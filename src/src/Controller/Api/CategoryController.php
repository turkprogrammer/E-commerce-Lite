<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Port\Repository\CategoryRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API контроллер для категорий
 */
#[AsController]
final class CategoryController extends AbstractApiController
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepo,
        protected SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    /**
     * Получить список всех категорий
     */
    #[Route('/api/categories', name: 'api_categories_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $categories = $this->categoryRepo->findActiveCategories();

        return $this->success([
            'categories' => $categories,
            'total' => count($categories),
        ], 'Категории получены', Response::HTTP_OK);
    }

    /**
     * Получить категорию по ID
     */
    #[Route('/api/categories/{id}', name: 'api_categories_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        // TODO: Добавить метод find в интерфейс (через отдельный адаптер)
        return $this->error('Не реализовано', Response::HTTP_NOT_IMPLEMENTED);
    }
}
