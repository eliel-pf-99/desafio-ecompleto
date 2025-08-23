<?php

namespace repositories;
require_once '../../vendor/autoload.php';

use App\Database\Database;
use App\Repositories\UtilityRepository;
use PHPUnit\Framework\TestCase;

/**
 * @class UtilityRepositoryTest
 * Classe de teste da UtilityRepository.
 */
class UtilityRepositoryTest extends TestCase
{
    private $mockDatabase;
    private $utilityRepository;

    /**
     * Configura a classe de teste antes do teste.
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->mockDatabase = $this->createMock(Database::class);
        $this->utilityRepository = new UtilityRepository($this->mockDatabase);
    }

    /**
     * @test
     * Testa o metodo getStoreIdsByGatewayId.
     */
    public function testGetStoreIdsByGatewayId(): void
    {
        $gatewayId = 1;
        $expectedResultFromDb = [
            ['id_loja' => 10],
            ['id_loja' => 20],
        ];

        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn($expectedResultFromDb);

        $result = $this->utilityRepository->getStoreIdsByGatewayId($gatewayId);
        $this->assertEquals([10, 20], $result);
    }

    /**
     * @test
     * Testa o metodo findIdByDescription.
     */
    public function testFindIdByDescription(): void
    {
        $description = 'descricao_teste';
        $table = 'tabela_teste';
        $expectedResultFromDb = [['id' => 42]];

        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn($expectedResultFromDb);

        $result = $this->utilityRepository->findIdByDescription($description, $table);
        $this->assertEquals(42, $result);
    }

    /**
     * @test
     * Testa o metodo findSituationById.
     */
    public function testFindSituationById(): void
    {
        $id = 123;
        $table = 'tabela_situacao';
        $expectedResultFromDb = [['descricao' => 'Concluído']];
        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn($expectedResultFromDb);
        $result = $this->utilityRepository->findSituationById($id, $table);
        $this->assertEquals('Concluído', $result);
    }
}

