<?php

namespace handlers;
require_once '../../vendor/autoload.php';

use App\Handlers\PaymentHandle;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @class PaymentHandleTest
 * Classe de teste da PaymentHandle.
 */
class PaymentHandleTest extends TestCase
{
    private $mockOrderPaymentRepo;
    private $mockOrderRepo;
    private $paymentHandle;

    /**
     * Configura a classe de teste antes do teste.
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->mockOrderPaymentRepo = $this->createMock(OrderPaymentRepository::class);
        $this->mockOrderRepo = $this->createMock(OrderRepository::class);
        $this->paymentHandle = new PaymentHandle($this->mockOrderPaymentRepo, $this->mockOrderRepo);
    }

    /**
     * @test
     * Testa o metodo handle bem sucedido.
     */
    public function testHandle(): void
    {
        $orderId = 'xyz-789';

        $payload = new stdClass();
        $payload->Error = false;
        $payload->Transaction_code = "00";
        $payload->Message = "Transacao Aprovada";

        $this->mockOrderPaymentRepo->method('findOrderPaymentIdByOrderId')
            ->with($orderId)
            ->willReturn(10);
        $this->mockOrderPaymentRepo->method('updateById')
            ->willReturn(true);

        $this->mockOrderRepo->method('updateById')
            ->with('id_situacao', [2, $orderId])
            ->willReturn(true);

        $result = $this->paymentHandle->handle($payload, $orderId);
        $this->assertEquals(['id' => $orderId, 'situacao' => 2], $result);
    }
}