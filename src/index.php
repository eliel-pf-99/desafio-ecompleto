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
    $baseRepository = new BaseRepository($db);

    try {
        
        $gatewayId = $baseRepository->findByDescription('PAGCOMPLETO', 'gateways');
        $paymentSituationId = $baseRepository->findByDescription('Aguardando Pagamento', 'pedido_situacao');
        $paymentWayId = $baseRepository->findByDescription('Cartão de Crédito', 'formas_pagamento');

        
        
        if (!$gatewayId) {
            throw new Exception("Gateway 'PAGCOMPLETO' não encontrado.");
        }

        if (!$paymentSituationId) {
            throw new Exception("Situação 'Aguardando Pagamento' não encontrada.");
        }

        if (!$paymentWayId) {
            throw new Exception("Forma de Pagamento 'Cartão de Crédito' não encontrada.");
        }

        
        
        $storeIds = $baseRepository->getStoreIdsByGatewayId($gatewayId);
        $orders = $orderRepo->getOrdersByStoreIds($storeIds);

        
        $filteredOrders = $orders->filterBySituation($paymentSituationId)->getData();

        
        $orderPayments = $orderPaymentRepo->getOrdersPaymentByStoreIds($filteredOrders)->filterByWay($paymentWayId)->getData();
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