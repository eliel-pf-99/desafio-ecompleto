<?php

/**
 * Classe DataCustomer, responsável por validar e tranferir dados do cliente.
 */
class DataCustomer
{
    private int $external_id;
    private string $name;
    private string $type;
    private string $email;
    private CustomerDocument $document;
    private string $birthday;

    private function __construct(int $external_id, string $name, string $type, string $email, string $birthday, CustomerDocument $document)
    {
        $this->external_id = $external_id;
        $this->name = $name;
        if($type == 'F'){
            $this->type = 'individual';
        } else {
            $this->type = 'corporation';
        }
        $this->email = $email;
        $this->birthday = $birthday;
        $this->document = $document;
    }

    /**
     * Method Factory para instanciar a classe DataCustomer
     * @param array $customer
     * @return DataCustomer
     */
    public static function create(array $customer): self
    {
        $external_id = $customer['id'];
        $name = $customer['nome'];
        $type = $customer['tipo_pessoa'];
        $email = $customer['email'];
        $birthday = $customer['data_nasc'];
        $documentNumber = $customer['cpf_cnpj'];

        if (!DataValidator::validatePersonType($type)) {
            throw new Exception("Tipo de pessoa inválido.");
        }

        if (!DataValidator::validateEmail($email)) {
            throw new Exception("E-mail inválido.");
        }

        if (!DataValidator::validateDate($birthday)) {
            throw new Exception("Data de nascimento inválida.");
        }

        try {
            $document = CustomerDocument::createFromNumber($documentNumber);
        } catch (Exception $e) {
            throw new Exception("Documento inválido: " . $e->getMessage());
        }

        return new self($external_id, $name, $type, $email, $birthday, $document);
    }

    /**
     * Função que transforma instância em um array.
     * @return array
     */
    public function toArray(): array {

        $result = [];
        $refletion = new ReflectionClass($this);
        foreach($refletion->getProperties() as $property){
            $property->setAccessible(true);
            if($property->getName() === 'document'){
                $result[$property->getName()] = $this->document->toArray();
                continue;
            }
            $result[$property->getName()] = $property->getValue($this);
        }
        return $result;
    }
}
