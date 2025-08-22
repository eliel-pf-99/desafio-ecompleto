<?php

/**
 * Controller responsável por verificar solicitação antes de enviar ao Service
 */
class PaymentController
{
    /**
     * @param PaymentService $paymentService
     * @param OrderRepository $orderRepo Repositório para a tabela pedidos.
     * @param UtilityRepository $utilityRepo Repositório para utilidades e buscas genéricas.
     */
    public function __construct(
        private PaymentService $paymentService,
        private OrderRepository $orderRepo,
        private UtilityRepository $utilityRepo,
        private DataDTO $dataDTO
    ) {}

    /**
     * Função responsável pela requisição de processamento de pagamento e seu
     * retorno.
     */
    public function processarPagamento(): void
    {
        header('Content-Type: application/json');
        $payload = json_decode(file_get_contents('php://input'), true);

        if (empty($payload) || !isset($payload['order_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos: ID do pedido ausente.']);
            return;
        }

        try {
            $result = $this->paymentService->processarPagamentos($payload);
            
            http_response_code(200);
            echo json_encode($result);

        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            http_response_code($statusCode);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }
}