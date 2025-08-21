<?php
/**
 * Classe base de repositório, tendo metodos auxíliares ao gerenciamento do banco de dados.
 */
class BaseRepository
{
    public function __construct(private Database $db){}

    /**
     * Função responsável por encontrar ids pela sua descrição
     * Tendo como argumento a descrição e o nome da tabela onde buscar.
     * @param string $description
     * @param string $table
     * @return int 
     */
    public function findByDescription(string $description, string $table): int{
        $safeTable = pg_escape_string($table);
        $result = $this->db->query("SELECT id FROM $safeTable WHERE descricao=?", [$description]);
        
        if(empty($result)){
            return null;
        }
        
        return $result[0]['id'];
    }

    /**
     * Função que busca pelas lojas se baseando id do gateway
     * @param int $gatewayId
     * @return array
     */
    public function getStoreIdsByGatewayId(int $gatewayId): array{
        $stores = $this->db->query("SELECT id_loja FROM lojas_gateway WHERE id_gateway=?", [$gatewayId]);

        return array_map(function($store){
            return $store['id_loja'];
        },$stores);
    }
}