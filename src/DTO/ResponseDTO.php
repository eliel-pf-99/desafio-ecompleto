<?php

class ResponseDTO
{
    public function __construct(private UtilityRepository $utilityRepo){}

    public function getResponseDTO(array $data){
        $situation = $this->utilityRepo->findSituationById($data['situacao'], 'pedido_situacao');
        return ['pedido_id' => $data['id'], 'situacao' => $situation];
    } 
}