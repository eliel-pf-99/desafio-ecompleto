<?php
namespace App\Controllers;

use App\Services\PaymentService;

/**
 * @class PaymentController
 * Controller responsável pela entrada e saída da requisição
 */
class PaymentController
{
    /**
     * @param PaymentService $paymentService
     */
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Função responsável pela requisição de processamento de pagamento e seu
     * retorno.
     * @throws /Exception
     */
    public function processarPagamento(array $requestData): void
    {
        if (!isset($requestData['order_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos: ID do pedido ausente.']);
            return;
        }

        $result = $this->paymentService->processarPagamentos($requestData);
        http_response_code(200);
        echo json_encode($result);
    }
}