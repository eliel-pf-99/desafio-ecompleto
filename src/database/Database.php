<?php

namespace App\Database;

require __DIR__ . '/../../vendor/autoload.php';

/**Carrega o monolog */

use App\Log\Loggers;
use Exception;
use Monolog\Logger;
use PDO;
use PDOException;


/**
 * @class Database
 * Classe responsável por gerenciar a conexão com o banco de dados.
 */
class Database
{
    private PDO $connection;
    private Logger $log;

    /**
     * Construtor responsável pela conexão com o banco de dados.
     *
     * @throws PDOException Se a conexão falhar.
     */
    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        $dsn = "pgsql:host={$host};dbname={$dbname}";

        $this->log = Loggers::getLogger();

        try {
            $this->connection = new PDO($dsn, $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->log->error("Não foi possível se conectar ao banco de dados");
            throw $e;
        }
    }

    /**
     * Retorna a instância da conexão PDO para ser usada por outros repositórios.
     *
     * @return PDO A instância da conexão.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
    * Função responsável por tratar e executar SQL no base de dados
     * @param string $sql
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->log->error("Erro na consulta do banco de dados: " . $sql . " " . $e->getMessage());
            throw new Exception("Erro no sistema. Tente mais tarde", 0, $e);
        }
    }
}