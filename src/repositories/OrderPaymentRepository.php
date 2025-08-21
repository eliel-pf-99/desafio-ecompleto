<?php

/**
 * Classe responsável por filtrar e tratar os pedidos de pagamentos que devem ser processados.
 */
class OrderPaymentRepository
{
    /** Array responsável pelo padrão builder */
    private array $ordersPayment;

    private const TABLENAME = 'pedidos_pagamentos';

    public function __construct(private Database $db){}

    /**
     * Função que inicia busca pelas lojas que devem ser filtradas,
     * implementando o padrão builder. 
     * Tendo como entrada uma lista com os ids das lojas a serem filtradas.
     * @param array $storesIds
     * @return OrderRepository
     */
    public function getOrdersPaymentByStoreIds(array $storeIds): self{
        global $tableName;
        if (empty($storeIds)) {
            throw new Exception("Não há lojas para ser filtrado.");
        }

        $sanitizedIds = array_filter($storeIds, 'is_numeric');
        $inClause = implode(',', $sanitizedIds);
        $table = self::TABLENAME;
        $sql = "SELECT * FROM $table WHERE id_pedido IN ({$inClause})";
        
        $this->ordersPayment = $this->db->query($sql);

        return $this;
    }

    /**
     * Função que é chamada após getOrdersPaymentByStoreIds
     * faz um filtro com quais dos pedidos deve ser retornado levando em consideração
     * a forma do pagamento.
     * Tendo como entrada o id da forma do pagamento.
     * Faz parte o padrão builder.
     * @param int $way
     * @return OrderPaymentRepository
     */
    public function filterByWay(int $way): self{
        $result = [];
        foreach($this->ordersPayment as $order){
            if($order['id_formapagto'] === $way){
                $result[] = $order;
            }
        }
        $this->ordersPayment = array_map(function($res){
            return $res['id'];
        },$result);

        return $this;
    }

    /**
     * Função que é chamada após filterByWay
     * retornado um array com todos os pedidos já filtrados e tratados.
     * Finaliza o padrão builder.
     * @return array
     */
    public function getData(){
        return $this->ordersPayment;
    }

    /**
     * Função que retornar uma OrderPayment buscando pelo Id
     * @param int $id
     * @return array
     */
    public function getOrderPaymentById(int $id): array{
        $tableName = self::TABLENAME;
        $orderPayment = $this->db->query("SELECT * FROM $tableName WHERE id=?", [$id]);
        if(!$orderPayment){
            throw new Exception("Pedido não encontrado.");
        }
        return $orderPayment[0];
    }
}