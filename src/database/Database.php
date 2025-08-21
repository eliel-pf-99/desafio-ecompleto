<?php



/**
 * Classe responsável por gerenciar a conexão com o banco de dados.
 */
class Database
{
    private PDO $connection;

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

        try {
            $this->connection = new PDO($dsn, $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados: " . $e->getMessage());
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

    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erro na consulta do banco de dados: " . $sql . " " . $e->getMessage());
        }
    }
}