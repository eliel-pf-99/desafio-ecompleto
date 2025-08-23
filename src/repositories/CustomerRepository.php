<?php

namespace App\Repositories;

/**
 * @class CustomerRepository
 * Classe responsável por gerenciar a Entidade Cliente
 */
class CustomerRepository extends BaseRepository
{
    protected string $table = 'clientes';
}
