<?php
/**
 * Classe base de repositório, sendo a base do gerenciamento do banco de dados.
 */
class BaseRepository
{
   protected Database $db;
   protected string $table;

   /** Construtor injetando a dependencia do banco de dados */
   public function __construct(Database $db){
        $this->db = $db;
   }

   /** Função base para encontrar por id */
   public function findById(int $id): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        
        if (empty($result)) {
            throw new Exception("Registro na tabela {$this->table} não encontrado.");
        }
        
        return $result[0];
    }
}