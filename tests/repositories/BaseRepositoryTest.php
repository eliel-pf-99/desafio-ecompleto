<?php

namespace repositories;
require_once '../../vendor/autoload.php';

use App\Database\Database;
use App\Repositories\BaseRepository;
use PHPUnit\Framework\TestCase;

/**
 * @class DummyRepository
 * Classe descartavel para teste da BaseRepository
 */
class DummyRepository extends BaseRepository
{
    protected string $table = 'dummy';
}

/**
 * @class BaseRepositoryTest
 * Classe de teste da BaseRepository
 */
class BaseRepositoryTest extends TestCase
{
    private $mockDatabase;
    private $dummyRepository;

    /**
     * Configura a classe de teste antes do teste.
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        $this->mockDatabase = $this->createMock(Database::class);
        $this->dummyRepository = new \DummyRepository($this->mockDatabase);
    }

    /**
     * @test
     *Testa o metodo findById bem sucedido
     */
    public function testFindByIdWithSuccess(): void
    {
        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn([
                ['id' => 1, 'name' => 'John Doe', 'id_situacao' => 1]
            ]);

        $order = $this->dummyRepository->findById(1);

        $this->assertIsArray($order);
        $this->assertArrayHasKey('name', $order);
        $this->assertEquals('John Doe', $order['name']);
    }

    /**
     * @test
     *Testa o metodo findById onde o id não existe
     */
    public function testFindByIdWithNotFindId(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erro interno do sistema. Tente novamente mais tarde.");


        $this->mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn([]);
        $this->dummyRepository->findById(1);
    }

    /**
     * Testa o método updateById bem sucedido.
     */
    public function testUpdateByIdWithSuccess(): void
    {
        $this->mockDatabase->expects($this->once())
            ->method('query');
        $id = 1;
        $data = ['nome' => 'Nome Atualizado'];
        $result = $this->dummyRepository->updateById($id, $data);
        $this->assertTrue($result);
    }
}
