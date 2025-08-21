<?php

/**
 * Classe responsável por filtrar e tratar os pedidos de pagamentos que devem ser processados.
 */
class OrderPaymentRepository extends BaseRepository
{
    protected string $table = 'pedidos_pagamentos';

    /**
     * Funcão que filtra por metodo de pagamento, no caso Cartão de crédito.
     * @param array $orders
     * @param int $paymentMethod
     * @return array
     */
    public function getOrdersWithPaymentCreditCard(array $orders, int $paymentMethod){
      $ordersId = array_column($orders, 'id');
      $placeholders = implode(',', array_fill(0, count($ordersId), '?'));  
      $sql = "SELECT id FROM {$this->table} WHERE id_pedido IN ({$placeholders}) AND id_formapagto=?";
      $args = array_merge($ordersId, [$paymentMethod]);
      $result = $this->db->query($sql, $args);
      return array_column($result, 'id');
    }

    /**
     * Função que retorna o id do pedido de pagamento buscando pelo id do pedido
     * @param string $paymentId
     * @return array
     */
    public function findOrderPaymentIdByOrderId(string $paymentId): int{
      $sql = "SELECT id FROM {$this->table} WHERE id_pedido = ?";
      $result = $this->db->query($sql, [$paymentId]);
      return $result[0]['id'];
    }
}