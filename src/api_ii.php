<?php

require_once __DIR__ . '\..\vendor\autoload.php';

class PaymentServiceOld
{
    private $orderRepo;
    private $orderPaymentRepo;
    private $customerRepo;

    public function __construct(
        OrderRepository $orderRepo,
        OrderPaymentRepository $orderPaymentRepo,
        CustomerRepository $customerRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->orderPaymentRepo = $orderPaymentRepo;
        $this->customerRepo = $customerRepo;
    }

    public function getPaymentData(int $orderPaymentId)
    {
        try {
            // 1. Buscar os dados das entidades
            $orderPayment = $this->orderPaymentRepo->getOrderPaymentById($orderPaymentId);
            $order = $this->orderRepo->getOderById($orderPayment['id_pedido']);
            $customer = $this->customerRepo->getCustomerById($order['id_cliente']);

            // 2. Preparar e retornar o payload
            return $this->prepareData($order, $orderPayment, $customer);

        } catch (Throwable $e) {
            // Lançar a exceção para que o código que chamou este método possa tratá-la
            throw new Exception("Falha ao preparar dados para pagamento: " . $e->getMessage());
        }
    }

    private function prepareData(array $order, array $payment, array $customer)
    {
        return [
            "external_order_id" => $payment['id'],
            "amount" => $order['valor_total'],
            "card_number" => $payment['num_cartao'],
            "card_cvv" => $payment['codigo_verificacao'],
            "card_expiration_date" => $this->vencimentoPrepare($payment['vencimento']),
            "card_holder_name" => $payment['nome_portador'],
            "customer" => [
                "external_id" => $customer['id'],
                "name" => $customer['nome'],
                "type" => $customer['tipo_pessoa'] == 'F' ? 'individual' : 'corporation',
                "email" => $customer['email'],
                "documents" => [
                    [
                        "type" => "cpf",
                        "number" => $customer['cpf_cnpj']
                    ]
                ],
                "birthday" => $customer['data_nasc']
            ]
        ];
    }
    
    private function vencimentoPrepare(string $data)
    {
        [$ano, $mes] = explode('-', $data);
        $lastTwoDigits = substr($ano, -2);
        return $mes . $lastTwoDigits; 
    }
}