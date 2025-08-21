<?php

/**
 * Classe DataOrder, responsável por validar e tranferir dados do pedido e do pagamento
 */
class DataOrder
{
    private function __construct(
        private int $external_order_id,
        private float $amount,
        private string $card_number,
        private string $card_cvv,
        private string $card_expiration_date,
        private string $card_holder_name
    ){}

    /**
     * Method Factory para instanciar a classe DataOrder
     * @param array $orderPayment
     * @param array $order
     * @return DataOrder
     */
    public static function create(array $orderPayment, array $order): self {

        $external_order_id = $orderPayment['id'];
        $amount = $order['valor_total'];
        $card_number = $orderPayment['num_cartao'];
        $card_cvv = $orderPayment['codigo_verificacao'];
        $card_expiration_date = $orderPayment['vencimento'];
        $card_holder_name = $orderPayment['nome_portador'];

        // Validação do cartão de crédito
        // if (!DataValidator::validateCreditCard($card_number)) {
        //     throw new Exception("Cartão de crédito inválido.");
        // }

        // Validação do CVV
        if (!DataValidator::validateCvv($card_cvv, $card_number)) {
            throw new Exception("CVV inválido.");
        }

        // Validação da data de validade
        if (empty($card_expiration_date)) {
            throw new Exception("Data de validade do cartão não pode estar vazia.");
        }
        
        // A data de validade vem no formato YYYY-MM
        // formato MMYY para usar a função isExpired
        $expirationPrepared = self::vencimentoPrepare($card_expiration_date);

        // if (DataValidator::isExpired($expirationPrepared)) {
        //     throw new Exception("Cartão com validade vencida.");
        // }

        // Validação de outros dados
        if (!$external_order_id) {
            throw new Exception("Id do pedido não pode ser nulo.");
        }

        if (!$amount) {
            throw new Exception("Valor do pedido não pode ser nulo.");
        }

        if (!$card_holder_name) {
            throw new Exception("Deve ser informado o nome do proprietário do cartão.");
        }

        return new self($external_order_id, $amount, $card_number, $card_cvv, $expirationPrepared, $card_holder_name);
    }


    /**
     * Função que converte o vencimento, para um valor compatível
     * Ex.: de 2022-08 para 0822
     * @param string $data
     * @return string
     */
    private static function vencimentoPrepare(string $data): string
    {
        [$ano, $mes] = explode('-', $data);
        $lastTwoDigits = substr($ano, -2);
        return $mes . $lastTwoDigits; 
    }

    /**
     * Função que transforma instância em um array.
     * @return array
     */
    public function toArray(): array{
        $result = [];
        $refletion = new ReflectionClass($this);
        foreach($refletion->getProperties() as $property){
            $property->setAccessible(true);
            $result[$property->getName()] = $property->getValue($this);
        }
        return $result;
    }
}