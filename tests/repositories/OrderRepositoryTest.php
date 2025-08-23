<?php

namespace repositories;
require_once "../../vendor/autoload.php";

use App\Database\Database;
use App\Repositories\OrderRepository;
use PHPUnit\Framework\TestCase;

/**
 * @class OrderRepositoryTest
 * Classe de teste da OrderRepository.
 */
class OrderRepositoryTest extends TestCase
{
    private $mockDatabase;
    private $orderRepository;

    /**
     * Configura a classe de teste antes do teste.
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        $this->mockDatabase = $this->createMock(Database::class);
        $this->dummyRepository = new OrderRepository($this->mockDatabase);
    }

    /**
     * @test
     * Testa se o método retorna a lista de IDs de pedidos.
     */
    public function testGetOrdersByStoreIdsAndSituation(): void
    {
        $storeIds = [1, 5, 8];
        $situation = 3;

        $expectedResultFromDb = [
            ['id' => 101],
            ['id' => 250],
            ['id' => 305],
        ];

        $expectedSql = "SELECT id FROM pedidos WHERE id_loja IN (?,?,?) AND id_situacao=?";
        $expectedArgs = array_merge($storeIds, [$situation]);

        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->with($expectedSql, $expectedArgs)
            ->willReturn($expectedResultFromDb);

        $actualResult = $this->orderRepository->getOrdersByStoreIdsAndSituation($storeIds, $situation);
        $this->assertEquals($expectedResultFromDb, $actualResult);
    }

    /**
     * @test
     * Testa se o método bem sucedido.
     */
    public function testUpdateOrderWithSuccess(): void
    {
        $params = [2, 123];
        $expectedSql = "UPDATE pedidos SET id_situacao = ? WHERE id = ?";
        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->with($expectedSql, $params)
            ->willReturn([]);
        $this->assertTrue($this->orderRepository->updateOrder($params));
    }

    /**
     * @test
     * Testa se o método se houver falha
     */
    public function testUpdateOrderRetornaFalseEmCasoDeFalha(): void
    {
        $params = [3, 456];
        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn(false);
        $this->assertFalse($this->orderRepository->updateOrder($params));
    }




