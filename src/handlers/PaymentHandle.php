<?php

/**Classe responsável por alterar a base de dados conforme resposta do Service */
class PaymentHandle
{
    public function __construct(){

    }

    public function handle(stdClass $payload, string $orderPaymentId){
        /**
         * { 
         *      "Error": false, 
         *      "Transaction_code": "00", 
         *      "Message": "Pagamento Aprovado"
         * } 
         * 
         */
        echo '<pre>';
        print_r($payload);
        echo '<\pre>';

        // if($payload['Error']){
        //     return;
        // }

        // if($payload['Transaction_code'] === "00" || $payload['Transaction_code'] === "01"){
        //     //atualizar pedido -> mudar na tabela pedidos situação para 1 ou 2 
        //     //atualizar pedido pagamento -> adicionando código e msg.
        // }

        // if($payload['Transaction_code'] === "02" || $payload['Transaction_code'] === "03" || $payload['Transaction_code'] === "04"){
        //     //cancelar pedido -> mudar na tabela pedidos situação para 3 - cancelado
        //     //atualizar pedido pagamento -> adicionando código e msg.
        // }

        

    }
}