<?php

/**
 * Classe CustomerDocument, responsável por validar e tranferir dados do documento do cliente.
 */
class CustomerDocument
{
    private function __construct(private string $number, private string $type){}

    /**
     * Method Factory para instanciar a classe CustomerDocument
     * A classe verifica e define o tipo de documento baseado em sua numeração
     * @param string $documentNumber
     * @return CustomerDocument
     */
    public static function createFromNumber(string $documentNumber): self
    {
        if (DataValidator::validateCpf($documentNumber)) {
            return new self($documentNumber, 'cpf');
        }

        if (DataValidator::validateRg($documentNumber)) {
            return new self($documentNumber, 'rg');
        }

        if (DataValidator::validateCnpj($documentNumber)) {
            return new self($documentNumber, 'cnpj');
        }

        throw new Exception("O número do documento é inválido.");
    }

    /**
     * Função que transforma instância em um array.
     * @return array
     */
    public function toArray(){
        $result = [];
        $refletion = new ReflectionClass($this);
        foreach($refletion->getProperties() as $property){
            $property->setAccessible(true);
            $result[$property->getName()] = $property->getValue($this);
        }
        return $result;
    }
}