<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Payment\ProcessPaymentWebhook;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * API контроллер для обработки webhook от платёжных систем
 */
#[AsController]
class WebhookController extends AbstractApiController
{
    public function __construct(
        private ProcessPaymentWebhook $processPaymentWebhook,
    ) {}

    /**
     * Обработать webhook от платёжной системы
     */
    #[Route('/api/webhooks/payment', name: 'api_webhooks_payment', methods: ['POST'])]
    public function handlePaymentWebhook(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $signature = $request->headers->get('X-Webhook-Signature', '');

        try {
            // Обрабатываем webhook
            $payment = $this->processPaymentWebhook->handle($payload);

            return $this->success([
                'payment' => $payment,
            ], 'Webhook обработан', Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->error('Ошибка обработки webhook', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Тест webhook endpoint
     */
    #[Route('/api/webhooks/payment/test', name: 'api_webhooks_payment_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->success([
            'status' => 'ok',
            'message' => 'Webhook endpoint работает',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ], 'Webhook endpoint доступен', Response::HTTP_OK);
    }
}
