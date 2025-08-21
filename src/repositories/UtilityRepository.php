<?php

require_once __DIR__ . '\..\..\vendor\autoload.php';

/**Carrega o monolog */
use Monolog\Logger;

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
        try{
            $result = $this->db->query($sql, [$description]);
            if (empty($result)) {
                throw new Exception("Descrição não encontrada.");
            }
        } catch(Exception $e){
            $this->log->error("Falha em executar busca: {$sql} - Erro: {$e->getMessage()}");
            throw new Exception("Erro no sistema. Tente mais tarde.", 0, $e);
        }
        
        
        
        return $result[0]['id'];
    }
}