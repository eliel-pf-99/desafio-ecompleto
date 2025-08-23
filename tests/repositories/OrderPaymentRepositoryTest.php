<?php

namespace repositories;
require_once '../../vendor/autoload.php';

use App\Database\Database;
use OrderPaymentRepository;
use PHPUnit\Framework\TestCase;

/**
 * @class OrderPaymentRepositoryTest
 * Classe de teste da OrderPaymentRepository.
 */
class OrderPaymentRepositoryTest extends TestCase
{
    private $mockDatabase;
    private $orderPaymentRepository;

    /**
     * Configura a classe de teste antes do teste.
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->mockDatabase = $this->createMock(Database::class);
        $this->orderPaymentRepository = new OrderPaymentRepository($this->mockDatabase);
    }

    /**
     * @test
     * Testa se o metodo getOrdersWithPaymentCreditCard retorna os IDs com forma de pagamento cartão de crédito.
     */
    public function testGetOrdersWithPaymentCreditCard(): void
    {
        $orders = [
            ['id' => '101'],
            ['id' => '202'],
            ['id' => '303'],
        ];
        $paymentMethod = 2;
        $expectedResultFromDb = [
            ['id' => 1001],
            ['id' => 1003],
        ];

        $expectedSql = "SELECT id FROM pedidos_pagamentos WHERE id_pedido IN (?,?,?) AND id_formapagto=?";
        $expectedArgs = ['101', '202', '303', 2];

        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->with($expectedSql, $expectedArgs)
            ->willReturn($expectedResultFromDb);

        $result = $this->orderPaymentRepository->getOrdersWithPaymentCreditCard($orders, $paymentMethod);

        $this->assertEquals([1001, 1003], $result);
    }


    /**
     * @test
     * Testa o metodo findOrderPaymentIdByOrderId.
     */
    public function testFindOrderPaymentIdByOrderId(): void
    {
        $orderId = 'abc-456';
        $expectedResultFromDb = [['id' => 999]];
        $expectedSql = "SELECT id FROM pedidos_pagamentos WHERE id_pedido = ?";
        $expectedArgs = ['abc-456'];

        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->with($expectedSql, $expectedArgs)
            ->willReturn($expectedResultFromDb);

        $result = $this->orderPaymentRepository->findOrderPaymentIdByOrderId($orderId);
        $this->assertEquals(999, $result);
    }
}
