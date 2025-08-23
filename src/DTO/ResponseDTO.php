<?php

namespace App\DTO;

use App\Repositories\UtilityRepository;

/**
 * @class ResponseDTO
 * Classe responsavél pela transferência de dados do retorno da implementação da API
 */
class ResponseDTO
{
    public function __construct(private UtilityRepository $utilityRepo){}

    /**
     * Função responsavél por gerar DTO de retorno da implementação da API
     * @param array $data
     * @return array
     */
    public function getResponseDTO(array $data){
        $situation = $this->utilityRepo->findSituationById($data['situacao'], 'pedido_situacao');
        return ['pedido_id' => $data['id'], 'situacao' => $situation];
    } 
}