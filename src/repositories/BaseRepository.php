<?php

namespace App\Repositories;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use App\Database\Database;
use Exception;
use App\Log\Loggers;
use Monolog\Logger;

/**
 * @class BaseRepository
 * Classe base de repositório, sendo a base do gerenciamento do banco de dados.
 */
abstract class BaseRepository
{
   protected Database $db;
   protected string $table;
   protected Logger $log;

   /** Construtor injetando a dependencia do banco de dados */
   public function __construct(Database $db){
        $this->db = $db;
        $this->log = Loggers::getLogger();
   }

   /**
    * Função base para encontrar por id
    * @param int $id
    * @return array
    * @throws Exception
    */
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

    /**
     * Função que altera o valor na coluna informada
     * @param string $column_name -> tabela que deve ser alterada.
     * @param array $params -> array contendo valores a serem atualizados.
     * @return bool -> retorna se a alteração foi feita com sucesso.
     */
    public function updateById(string $column_name, array $params){
      $sql = "UPDATE {$this->table} SET {$column_name} = ? WHERE id = ?";
      $result = $this->db->query($sql, $params);
      return ($result !== false);
    }
}