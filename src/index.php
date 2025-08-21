<?php

require_once __DIR__ . '\..\vendor\autoload.php';

/**Carregamento das variaveis de ambiente. */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/** Conexão com banco de dados através da Classe Database */
$db = new Database();


function getOrdersId(){
    global $db;
    $customerRepo = new CustomerRepository($db);
    $orderRepo = new OrderRepository($db);
    $orderPaymentRepo = new OrderPaymentRepository($db);
    $utilityRepo = new UtilityRepository($db);

    try {
        
        $gatewayId = $utilityRepo->findIdByDescription('PAGCOMPLETO', 'gateways');
        $paymentSituationId = $utilityRepo->findIdByDescription('Aguardando Pagamento', 'pedido_situacao');
        $paymentWayId = $utilityRepo->findIdByDescription('Cartão de Crédito', 'formas_pagamento');

        
        
        if (!$gatewayId) {
            throw new Exception("Gateway 'PAGCOMPLETO' não encontrado.");
        }

        if (!$paymentSituationId) {
            throw new Exception("Situação 'Aguardando Pagamento' não encontrada.");
        }

        if (!$paymentWayId) {
            throw new Exception("Forma de Pagamento 'Cartão de Crédito' não encontrada.");
        }

        
        
        $storeIds = $utilityRepo->getStoreIdsByGatewayId($gatewayId);
        $orders = $orderRepo->getOrdersByStoreIdsAndSituation($storeIds, $paymentSituationId);
        $orderPayments = $orderPaymentRepo->getOrdersWithPaymentCreditCard($orders, $paymentWayId);
        
        $dto = new DataDTO($orderRepo, $orderPaymentRepo, $customerRepo);
        $data = $dto->generateData($orderPayments[1]);
        echo "<pre>";
        print_r($data);
        echo "<\pre>";

        

    } catch (Exception $e) {
        echo "Ocorreu um erro: " . $e->getMessage();
    }
}

getOrdersId();