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
            $orderId = (int) $payload['order_id'];

            $order = $this->orderRepo->findById($orderId);
            
            $processingStatusId = $this->utilityRepo->findIdByDescription('Aguardando Pagamento', 'pedido_situacao');
            
            if ($order['id_situacao'] !== $processingStatusId) {
                http_response_code(409);
                echo json_encode(['error' => 'Pedido não está no estado correto para processamento.']);
                return;
            }
            
            $pagcompletoGatewayId = $this->utilityRepo->findIdByDescription('PAGCOMPLETO', 'gateways');
            $validStores = $this->utilityRepo->getStoreIdsByGatewayId($pagcompletoGatewayId);
            
            if (!in_array($order['id_loja'], $validStores)) {
                http_response_code(403);
                echo json_encode(['error' => 'Loja não autorizada a usar este gateway.']);
                return;
            }

            $data = $this->dataDTO->generateData($orderId);
            $result = $this->paymentService->processarPagamentos($data);
            
            http_response_code(200);
            echo json_encode($result);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }
}