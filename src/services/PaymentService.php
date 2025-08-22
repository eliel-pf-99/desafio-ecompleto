<?php

require_once __DIR__ . '\..\..\vendor\autoload.php';

/**Carrega o monolog */
use Monolog\Logger;

/**
 * Classe responsável por fazer chamada na API e direcionar a resposta ao Handler
 */
class PaymentService{
    private Logger $log;
    private string $api_endpoint;

    /** 
     * Construtor que obtém instancia do Logger e declara a url do endpoint 
     * @param PaymentHandle $handler
     * */
    public function __construct(private PaymentHandle $handler, private ResponseDTO $responseDTO){
        $this->log = Loggers::getLogger();
        $this->api_endpoint = $_ENV['API_ENDPOINT'] . $_ENV['API_TOKEN'];
    }

    /**
     * Função responsável pela chamada para API de processamento
     * Configura e realiza a chamada retronando a resposta
     * @param array $payload
     * @return json $response
     */
    private function chamadaAPI($payload){
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
     * Função responsável por gerenciar o processamento de pagamento.
     * Obtém o retorno da chamada da API e encaminha para o Handler.
     * @param array $data
     * @return array
     */
    public function processarPagamentos(array $data): array{
        try{
            $response = $this->chamadaAPI($data);
            $decodedResponse = json_decode($response);
            $result = $this->handler->handle($decodedResponse, $data['external_order_id']);
            return $this->responseDTO->getResponseDTO($result);
        } catch(Exception $e){
            $this->log->error("Erro na chamada da API: {$e->getMessage()}");
            throw new Exception("Erro no sistema. Tente mais tarde.", 0, $e);
        }   
    }
}