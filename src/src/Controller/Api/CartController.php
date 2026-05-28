<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Cart\AddItemToCart;
use App\Application\Cart\GetCart;
use App\Application\Cart\RemoveItemFromCart;
use App\Domain\Exception\CartNotFoundException;
use App\Domain\Exception\ProductNotFoundException;
use App\Domain\Exception\ProductNotActiveException;
use App\Domain\Exception\InsufficientStockException;
use App\Domain\Exception\CartItemNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API контроллер для управления корзиной
 */
#[AsController]
class CartController extends AbstractApiController
{
    public function __construct(
        private GetCart $getCart,
        private AddItemToCart $addItemToCart,
        private RemoveItemFromCart $removeItemFromCart,
        protected SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    /**
     * Получить текущую корзину
     */
    #[Route('/api/cart', name: 'api_cart_get', methods: ['GET'])]
    public function getCart(Request $request): JsonResponse
    {
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        $sessionId = $session->getId();
        
        // Инициализируем сессию для отправки cookie
        $session->set('_cart_initialized', true);
        
        $cart = $this->getCart->handle($sessionId);

        return $this->success([
            'cart' => [
                'id' => $cart->getId(),
                'sessionId' => $cart->getSessionId(),
                'items' => $cart->getItems(),
                'totalAmount' => $cart->getTotalAmount(),
                'totalItems' => $cart->getTotalItems(),
                'isEmpty' => $cart->isEmpty(),
            ],
        ], 'Корзина получена', Response::HTTP_OK);
    }

    /**
     * Добавить товар в корзину
     */
    #[Route('/api/cart/items', name: 'api_cart_add', methods: ['POST'])]
    public function addItem(Request $request): JsonResponse
    {
        $data = $request->toArray();
        
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        $sessionId = $session->getId();
        
        $productId = $data['productId'] ?? 0;
        $quantity = $data['quantity'] ?? 1;

        try {
            $cartItem = $this->addItemToCart->handle($sessionId, $productId, $quantity);
            $cart = $cartItem->getCart();

            return $this->success([
                'item' => $cartItem,
                'cart' => [
                    'totalAmount' => $cart->getTotalAmount(),
                    'totalItems' => $cart->getTotalItems(),
                ],
            ], 'Товар добавлен в корзину', Response::HTTP_CREATED);
        } catch (ProductNotFoundException $e) {
            return $this->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (ProductNotActiveException|InsufficientStockException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Удалить товар из корзины
     */
    #[Route('/api/cart/items/{id}', name: 'api_cart_remove', methods: ['DELETE'])]
    public function removeItem(Request $request, int $id): JsonResponse
    {
        $sessionId = $request->getSession()->getId();

        try {
            $this->removeItemFromCart->handle($sessionId, $id);
            return $this->success([], 'Товар удален из корзины', Response::HTTP_OK);
        } catch (CartNotFoundException $e) {
            return $this->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (CartItemNotFoundException $e) {
            return $this->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
