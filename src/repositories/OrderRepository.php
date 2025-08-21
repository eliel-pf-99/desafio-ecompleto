<?php

/**
 * Classe responsável por filtrar e tratar os pedidos que devem ser processados.
 */
class OrderRepository extends BaseRepository
{
   protected string $table = 'pedidos';

   /** Função que retorna uma lista com pedidos em pertencete a lojas que 
   * adrem ao sistema PAGCOMPLETO e estejam em situação de processamento. 
   * @param array $storeIds
   * @param int $situation
   * @return array
   */
   public function getOrdersByStoreIdsAndSituation(array $storeIds, int $situation): array{
      $placeholders = implode(',', array_fill(0, count($storeIds), '?'));  
      $sql = "SELECT id FROM {$this->table} WHERE id_loja IN ({$placeholders}) AND id_situacao=?";
      $args = array_merge($storeIds, [$situation]);
      return $this->db->query($sql, $args);
   }

   /**
     * Função que altera o valor na coluna id_situacao
     * @param array $params -> array contendo valores a serem atualizados.
     * @return bool -> retorna se a alteração foi feita com sucesso.
     */
    public function updateOrder(array $params): bool{
      $sql = "UPDATE {$this->table} SET id_situacao = ? WHERE id = ?";
      $result = $this->db->query($sql, $params);
      return ($result !== false);
    }
}