<?php
namespace controllers;
require_once '../../vendor/autoload.php';

use App\Controllers\PaymentController;
use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentControllerTest extends TestCase
{
    private $mockPaymentService;
    private PaymentController $controller;

    protected function setUp(): void
    {
        $this->mockPaymentService = $this->createMock(PaymentService::class);
        $this->controller = new PaymentController($this->mockPaymentService);
    }

    public function testProcessarPagamentoComSucesso(): void
    {
        $requestData = ['order_id' => 123];
        $serviceResponse = ['status' => 'success', 'data' => ['id' => 'abc-123', 'situacao' => 2]];
        $expectedOutput = json_encode($serviceResponse);

        $this->mockPaymentService->method('processarPagamentos')
            ->with(['order_id' => 123])
            ->willReturn($serviceResponse);

        $this->expectOutputString($expectedOutput);

        $this->controller->processarPagamento($requestData);
    }
}

