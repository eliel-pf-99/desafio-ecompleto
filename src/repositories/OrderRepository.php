<?php

/**
 * Classe responsável por filtrar e tratar os pedidos que devem ser processados.
 */
class OrderRepository
{
    /** Array responsável pelo padrão builder */
    private array $orders;

    const TABLENAME = 'pedidos';

    public function __construct(private Database $db){}

    /**
     * Função que inicia busca pelas lojas que devem ser filtradas,
     * implementando o padrão builder. 
     * Tendo como entrada uma lista com os ids das lojas a serem filtradas.
     * @param array $storesIds
     * @return OrderRepository
     */
    public function getOrdersByStoreIds(array $storeIds){
        if (empty($storeIds)) {
            return [];
        }

        $sanitizedIds = array_filter($storeIds, 'is_numeric');
        $inClause = implode(',', $sanitizedIds);
        $table = self::TABLENAME;
        $sql = "SELECT * FROM $table WHERE id_loja IN ({$inClause})";
        
        $this->orders = $this->db->query($sql);
        
        return $this;
    }

    /**
     * Função que é chamada após getOrdersByStoreIds
     * faz um filtro com quais dos pedidos deve ser retornado levando em consideração
     * o situação do pedido.
     * Tendo como entrada o id da situação do pedido.
     * Faz parte o padrão builder.
     * @param int $situation
     * @return OrderRepository
     */
    public function filterBySituation(int $situation): self{
        $result = [];
        foreach($this->orders as $order){
            if($order['id_situacao'] === $situation){
                $result[] = $order;
            }
        }
        $this->orders = array_map(function($res){
            return $res['id'];
        },$result);
        return $this;
    }

    /**
     * Função que é chamada após filterBySituation
     * retornado um array com todos os pedidos já filtrados e tratados.
     * Finaliza o padrão builder.
     * @return array
     */
    public function getData(){
        return $this->orders;
    }

    /**
     * Função que retornar uma Order buscando pelo Id
     * @param int $id
     * @return array
     */
    public function getOderById(int $id): array{
        $table = self::TABLENAME;
        $order = $this->db->query("SELECT * FROM $table WHERE id=?", [$id]);
        if(!$order){
            throw new Exception("Pedido não encontrado.");
        }
        return $order[0];
    }
}