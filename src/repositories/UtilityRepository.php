<?php
/** Classe de utilitarios para auxilio dos repositorios. */
class UtilityRepository extends BaseRepository
{
    /**
     * Função que busca as lojas que usam o serviço PAGCOMPLETO
     * @param int $gatewayId
     * @return array
     *  */
    public function getStoreIdsByGatewayId(int $gatewayId): array
    {
        $sql = "SELECT id_loja FROM lojas_gateway WHERE id_gateway = ?";
        $stores = $this->db->query($sql, [$gatewayId]);
        
        return array_map(function($store) {
            return $store['id_loja'];
        }, $stores);
    }
    
    /**
     * Função que busca as id pelo descrição
     * @param string $description
     * @param string $table
     * @return int
     *  */
    public function findIdByDescription(string $description, string $table): int
    {
        $sql = "SELECT id FROM $table WHERE descricao = ?";
        $result = $this->db->query($sql, [$description]);
        
        if (empty($result)) {
            throw new Exception("Descrição não encontrada.");
        }
        
        return $result[0]['id'];
    }
}