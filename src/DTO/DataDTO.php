<?php

namespace App\DTO;

use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CustomerRepository;
use \Exception;
/**
 * @class DataDTO
 * Classe responsável por realizar a transferência dos dados do pedido e cliente
 * para chamada de API
 */
class DataDTO
{

    private array $order;
    private array $orderPayment;
    private array $customer;

    public function __construct(
        private OrderRepository $orderRepo, 
        private OrderPaymentRepository $orderPaymentRepo,    
        private CustomerRepository $customerRepo,    
    ){}

    /**
     * Função que cria um array com os dados do pedidos_pagamentos e pedidos
     * @return array
     * @throws Exception
     */
    private function createDataOrder(): array{
        try{
            $dataOrder = DataOrder::create($this->orderPayment, $this->order);
            return $dataOrder->toArray();
        } catch(Excepetion $e){
            throw new Exception("Dados do pagador inválidos: " . $e->getMessage());
        }
    }

    /**
     * Função que cria um array com os dados do cliente
     * @return array
     * @throws Exception
     */
    private function createDataCustomer(): array{
        try{
            $dataCustomer = DataCustomer::create($this->customer);
            return $dataCustomer->toArray();
        } catch(Excpetion $e){
            throw new Exception("Dados do cliente inválidos: " . $e->getMessage(), 400);
        }
    }

    /**
     * Função que retorna um array com os dados processados para chamada API
     * @param int $orderPaymentId
     * @return array
     */
    public function generateData(int $orderId): array{
        try{
            $this->order = $this->orderRepo->findById($orderId);
            $this->orderPayment = $this->orderPaymentRepo->findById($this->orderPaymentRepo->findOrderPaymentIdByOrderId($orderId));
            $this->customer = $this->customerRepo->findById($this->order['id_cliente']);
    
            $orderData = $this->createDataOrder();
            $customerData = ['customer' => $this->createDataCustomer()];
    
            return array_merge($orderData, $customerData);
            
        } catch(Excpetion $e){
            throw new Exception("Dados do cliente inválidos: " . $e->getMessage(), 400);
        }
    }

}