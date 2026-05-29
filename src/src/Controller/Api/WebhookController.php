<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Payment\ProcessPaymentWebhook;
use App\Domain\Exception\PaymentNotFoundException;
use App\Domain\Exception\WebhookException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API контроллер для обработки webhook от платёжных систем
 */
#[AsController]
final class WebhookController extends AbstractApiController
{
    public function __construct(
        private ProcessPaymentWebhook $processPaymentWebhook,
        protected SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    /**
     * Обработать webhook от платёжной системы
     *
     * @return JsonResponse
     */
    #[Route('/api/webhooks/payment', name: 'api_webhooks_payment', methods: ['POST'])]
    public function handlePaymentWebhook(Request $request): JsonResponse
    {
        $payload = $request->toArray();

        try {
            // Обрабатываем webhook
            $payment = $this->processPaymentWebhook->handle($payload);

            return $this->success([
                'payment' => $payment,
            ], 'Webhook обработан', Response::HTTP_OK);
        } catch (WebhookException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (PaymentNotFoundException $e) {
            return $this->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error('Ошибка обработки webhook', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Тест webhook endpoint
     *
     * @return JsonResponse
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
