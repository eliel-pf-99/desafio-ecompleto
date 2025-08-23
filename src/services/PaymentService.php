<?php

namespace App\Services;
require_once __DIR__ . '\..\..\vendor\autoload.php';

use App\Handlers\PaymentHandle;
use App\DTO\ResponseDTO;
use App\DTO\DataDTO;
use App\Repositories\OrderRepository;
use App\Repositories\UtilityRepository;
use App\Log\Loggers;
use Monolog\Logger;
use stdClass;
use Exception;

/**
 * @class PaymentService
 * classe responsável pelas regras de negocio.
 */
class PaymentService{
    private Logger $log;
    private string $api_endpoint;

    /** 
     * Construtor que obtém instancia do Logger e declara a url do endpoint
     * */
    public function __construct(
        private PaymentHandle $handler,
        private DataDTO $dataDTO,
        private ResponseDTO $responseDTO,
        private UtilityRepository $utilityRepo,
        private OrderRepository $orderRepo){
        $this->log = Loggers::getLogger();
        $this->api_endpoint = $_ENV['API_ENDPOINT'] . $_ENV['API_TOKEN'];
    }

    /**
     * Função responsável pela chamada para API de processamento
     * Configura e realiza a chamada retronando a resposta
     * @param array $payload
     * @return json $response
     * @throws Exception
     */
    protected function chamadaAPI($payload){
        $jsonPayload = json_encode($payload);

        $ch = curl_init($this->api_endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', 
            'Content-Length: ' . strlen($jsonPayload) 
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            throw new Exception($error_msg);
        }

        curl_close($ch);
        return $response;
        
    }

    /**
     * Função que verifica se a situaçõ do pedido enviado é de 'Aguardando Pagamento'
     * @param array $order
     * @return void
     * @throws Exception
     */
    private function filterSituation(array $order){
            $processingStatusId = $this->utilityRepo->findIdByDescription('Aguardando Pagamento', 'pedido_situacao');

            if ($order['id_situacao'] !== $processingStatusId) {
                throw new Exception('Pedido não está no estado correto para processamento.', 409);
            }
    }

    /**
     * Função que verifica se o pedido enviado pertence as lojas que implementam a API
     * @param array $order
     * @return void
     * @throws Exception
     */
    private function filterStores(array $order){
        $pagcompletoGatewayId = $this->utilityRepo->findIdByDescription('PAGCOMPLETO', 'gateways');
        $validStores = $this->utilityRepo->getStoreIdsByGatewayId($pagcompletoGatewayId);
        
        if (!in_array($order['id_loja'], $validStores)) {
            throw new Exception('Loja não autorizada a usar este gateway.', 403);
        }
    }

    /**
     * Função que valida o payload recebido pelo Controller
     * @param array $payload
     * @return array
     * @throws Exception
     */
    protected function validatePayload(array $payload): array{
            $orderId = (int) $payload['order_id'];
            $order = $this->orderRepo->findById($orderId);

            $this->filterSituation($order);
            $this->filterStores($order);

            $data = $this->dataDTO->generateData($orderId);
            return $data;   
    }

    /**
     * Função responsável por gerenciar o processamento de pagamento.
     * Obtém o retorno da chamada da API e encaminha para o Handler.
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function processarPagamentos(array $payload): array{
        try{
            $data = $this->validatePayload($payload);
            $response = $this->chamadaAPI($data);
            $decodedResponse = json_decode($response);
            $result = $this->handler->handle($decodedResponse, $data['external_order_id']);
            return $this->responseDTO->getResponseDTO($result);
        } catch(Exception $e){
            $this->log->error("Erro na chamada da API: {$e->getMessage()}");
            $statusCode = $e->getCode() ?: false;
            if($statusCode){
                throw $e;
            }
            
            throw new Exception("Erro no sistema. Tente mais tarde.", 0, $e);
        }   
    }
}