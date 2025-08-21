<?php

require_once __DIR__ . '\..\..\vendor\autoload.php';

/**Carrega o monolog */
use Monolog\Logger;

/**
 * Classe base de repositório, sendo a base do gerenciamento do banco de dados.
 */
class BaseRepository
{
   protected Database $db;
   protected string $table;
   protected Logger $log;

   /** Construtor injetando a dependencia do banco de dados */
   public function __construct(Database $db){
        $this->db = $db;
        $this->log = Loggers::getLogger();
   }

   /** Função base para encontrar por id */
   public function findById(int $id): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        try{
            $result = $this->db->query($sql, [$id]);
            if (empty($result)) {
                throw new Exception("Registro na tabela {$this->table} não encontrado.");
            }
            return $result[0];
        } catch(Exception $e){
            $this->log->error("Falha em executar busca: {$sql} - Erro: {$e->getMessage()}");
            throw new Exception("Erro interno do sistema. Tente novamente mais tarde.", 0, $e);
        }
        
    }
}