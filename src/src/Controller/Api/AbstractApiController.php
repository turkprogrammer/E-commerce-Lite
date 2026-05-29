<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Базовый контроллер для API
 */
abstract class AbstractApiController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * Создать JSON ответ
     *
     * @param array<string, string> $headers
     * @param array<string, mixed> $context
     */
    protected function json(mixed $data, int $status = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse
    {
        $context['groups'] = $context['groups'] ?? ['api_read'];

        return parent::json($data, $status, $headers, $context);
    }

    /**
     * Создать ответ с ошибкой
     *
     * @param array<int, array{field: string, message: string}> $errors
     */
    protected function error(string $message, int $status = Response::HTTP_BAD_REQUEST, array $errors = []): JsonResponse
    {
        $data = [
            'error' => true,
            'message' => $message,
            'status' => $status,
        ];

        if (!empty($errors)) {
            $data['errors'] = $errors;
        }

        return $this->json($data, $status);
    }

    /**
     * Создать ответ об успехе
     *
     * @param array<string, mixed> $context
     */
    protected function success(mixed $data = null, string $message = 'OK', int $status = Response::HTTP_OK, array $context = []): JsonResponse
    {
        $response = [
            'error' => false,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $this->json($response, $status, [], $context);
    }

}
