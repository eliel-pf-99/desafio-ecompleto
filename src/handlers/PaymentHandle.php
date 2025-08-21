<?php

/**Classe responsável por alterar a base de dados conforme resposta do Service */
class PaymentHandle
{
    public function __construct(private OrderPaymentRepository $orderPaymentRepo, private OrderRepository $orderRepo){}

    /**
     * Função que atualiza a tabela pedidos_pagamentos
     * @param int $orderPaymentId -> id da row que deve ser alterado
     * @param stdClass $value -> valores que serão atualizados
     * @return bool -> retorna se não houve falha na atualização
     */
    private function updateOrderPayment(int $orderPaymentId, stdClass $value): bool {
        //deve se alterar na coluna retorno_intermediador o retorno da API 
        $content = json_encode($value);
        return $this->orderPaymentRepo->updateById('retorno_intermediador', ["$content", $orderPaymentId]); 
    }

    /**
     * Função que atualiza a tabela pedidos
     * @param string $orderId -> id da row que deve ser alterado
     * @param int $situation -> valor que sera atualizados
     * @return bool -> retorna se não houve falha na atualização
     */
    private function updateOrder(string $orderId, int $situation){
        return $this->orderRepo->updateById('id_situacao',[$situation, $orderId]);
    }

    /**
     * Função handle, lida com o retorno do service direcionando o resulto para alteração correta no banco de dados.
     * @param stdClass $payload -> resposta da API
     * @param string $orderId -> id do pedido
     */
    public function handle(stdClass $payload, string $orderId){
        if($payload->Error){
            throw new Exception("Erro no servidor. {$payload->Message}");
        }

        $orderPaymentId = $this->orderPaymentRepo->findOrderPaymentIdByOrderId($orderId);
        
        $updatePayment = $this->updateOrderPayment($orderPaymentId, $payload);
        
        if(!$updatePayment){
            throw new Exception("Falha ao atualizar a tabela pedidos_pagamentos");
        }
        
        $updateOrder = match($payload->Transaction_code){
            "00" => $this->updateOrder($orderId, 2),
            "01" => $this->updateOrder($orderId, 1),
            "02", "03", "04" => $this->updateOrder($orderId, 3),
        };

        if(!$updateOrder){
            throw new Exception("Falha ao atualizar a tabela pedidos");
        }

        
    }
}