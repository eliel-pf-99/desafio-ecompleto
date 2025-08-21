<?php
/** Classe responsável por gerenciar a Entidade Cliente */
class CustomerRepository
{
    public function __construct(private Database $db){}

    /**
     * Função que retorna um cliente pelo Id
     * @param int $id
     * @return array
     */
    public function getCustomerById(int $id): array{
        $cliente = $this->db->query("SELECT * FROM clientes WHERE id=:id", [$id]);
        if(!$cliente){
            throw new Exception("Cliente não encontrado.");
        }
        return $cliente[0];
    }
}