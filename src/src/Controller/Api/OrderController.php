<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Cart\CheckoutCart;
use App\Application\Cart\AddItemToCart;
use App\Application\Order\GetOrderByNumber;
use App\Application\Order\GetOrdersByEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API контроллер для управления заказами
 */
class OrderController extends AbstractApiController
{
    public function __construct(
        private CheckoutCart $checkoutCart,
        private AddItemToCart $addItemToCart,
        private GetOrdersByEmail $getOrdersByEmail,
        private GetOrderByNumber $getOrderByNumber,
        protected SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    /**
     * Создать новый заказ (Checkout) или получить заказы по email
     */
    #[Route('/api/orders', name: 'api_orders', methods: ['POST', 'GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->isMethod('POST')) {
            return $this->create($request);
        }

        return $this->list($request);
    }

    /**
     * Создать новый заказ (Checkout)
     */
    private function create(Request $request): JsonResponse
    {
        $data = $request->toArray();
        
        // Запускаем сессию если еще не запущена
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        $sessionId = $session->getId();

        // Проверяем данные покупателя
        $requiredFields = ['customerName', 'customerEmail', 'customerPhone', 'deliveryAddress'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->error("Поле '{$field}' обязательно", Response::HTTP_BAD_REQUEST);
            }
        }

        // Проверяем товары
        if (empty($data['items']) || !is_array($data['items'])) {
            return $this->error('Корзина пуста', Response::HTTP_BAD_REQUEST);
        }

        try {
            // Сначала добавляем товары в корзину
            foreach ($data['items'] as $item) {
                $this->addItemToCart->handle(
                    $sessionId,
                    (int)$item['productId'],
                    (int)($item['quantity'] ?? 1)
                );
            }

            // Оформляем заказ через CheckoutCart Use Case
            $order = $this->checkoutCart->handle($sessionId, $data);

            return $this->success([
                'order' => [
                    'orderNumber' => $order->getOrderNumber(),
                    'totalAmount' => $order->getTotalAmount(),
                    'customerName' => $order->getCustomerName(),
                    'customerEmail' => $order->getCustomerEmail(),
                ],
            ], 'Заказ успешно создан', Response::HTTP_CREATED);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Получить заказы по email
     */
    private function list(Request $request): JsonResponse
    {
        $email = $request->query->get('email');

        if (!$email) {
            return $this->error('Email параметр обязателен', Response::HTTP_BAD_REQUEST);
        }

        $orders = $this->getOrdersByEmail->handle($email);

        return $this->success([
            'orders' => $orders,
            'total' => count($orders),
        ], 'Заказы получены', Response::HTTP_OK);
    }

    /**
     * Получить заказ по номеру
     */
    #[Route('/api/orders/{orderNumber}', name: 'api_orders_show', methods: ['GET'])]
    public function show(string $orderNumber): JsonResponse
    {
        try {
            $order = $this->getOrderByNumber->handle($orderNumber);

            return $this->success([
                'order' => $order,
            ], 'Заказ найден', Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
