<?php

require_once __DIR__ . '\vendor\autoload.php';

use App\Router\Router;
use App\Controllers\PaymentController;
use App\Services\PaymentService;
use App\Handlers\PaymentHandle;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\UtilityRepository;
use App\DTO\DataDTO;
use App\DTO\ResponseDTO;
use App\Database\Database;


$paymentUri = "/desafio-ecompleto/api/pagamento";
  

if($_SERVER['REQUEST_METHOD'] === 'POST' && strtok($_SERVER['REQUEST_URI'], '?') === $paymentUri) {

    /**Carregamento das variaveis de ambiente. */
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    /** Conexão com banco de dados através da Classe Database */
    $db = new Database();

    /**Instancia as dependencias */
    $customerRepo = new CustomerRepository($db);
    $orderRepo = new OrderRepository($db);
    $orderPaymentRepo = new OrderPaymentRepository($db);
    $utilityRepo = new UtilityRepository($db);
    $dataDTO = new DataDTO($orderRepo, $orderPaymentRepo, $customerRepo);
    $responseDTO = new ResponseDTO($utilityRepo);

    /**Instancia o Handler */
    $paymentHandler = new PaymentHandle($orderPaymentRepo, $orderRepo);

    /**Instancia o Service */
    $paymentService = new PaymentService(
        $paymentHandler,
        $dataDTO,
        $responseDTO,
        $utilityRepo,
        $orderRepo
    );

    /**Instancia o Controller */
    $paymentController = new PaymentController($paymentService, $orderRepo, $utilityRepo, $dataDTO);

    /**Intancia o Router */
    $router = new Router();

    /**declara a rota para o processamento do pagamento */
    $router->post($paymentUri, [$paymentController, 'processarPagamento']);

    /** Dispacha a requisição para a função */
    try{
        $router->dispatch();
    } catch (Exception $e) {
        $statusCode = $e->getCode() ?: 500;
        http_response_code($statusCode);
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }

} else {
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desafio e-completo</title>
</head>
<body>
    <h2>Desafio E-completo</h2>
</body>
</html>


<?php } ?>
