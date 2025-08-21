<?php

require_once __DIR__ . '\..\..\vendor\autoload.php';

/**Carrega o monolog */
use Monolog\Logger;

/**
 * Classe responsável por fazer chamada na API e direcionar a resposta
 */
class PaymentService{
    private Logger $log;
    private string $api_endpoint;

    public function __construct(private PaymentHandle $handler){
        $this->log = Loggers::getLogger();
        $this->api_endpoint = $_ENV['API_ENDPOINT'] . $_ENV['API_TOKEN'];
    }

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

    public function processarPagamentos(array $data){
        try{
            $response = $this->chamadaAPI($data);
            $decodedResponse = json_decode($response);
            $this->handler->handle($decodedResponse, $data['external_order_id']);
        } catch(Exception $e){
            $this->log->error("Erro na chamada da API: {$e->getMessage()}");
            throw new Exception("Erro no sistema. Tente mais tarde.", 0, $e);
        }   
    }
}