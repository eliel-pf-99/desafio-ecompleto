<?php

namespace services;
require_once '../../vendor/autoload.php';

use stdClass;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderPaymentRepository;
use App\Services\PaymentService;
use App\DTO\DataDTO;
use App\DTO\ResponseDTO;
use App\Repositories\OrderRepository;
use App\Repositories\UtilityRepository;
use App\Handlers\PaymentHandle;
use \PHPUnit\Framework\TestCase;

/**
 * @class PaymentServiceTest
 * Teste unitário focado no método processarPagamentos().
 */
class PaymentServiceTest extends TestCase
{
    private $mockPaymentHandle;
    private $mockDataDTO;
    private $mockResponseDTO;
    private $mockUtilityRepo;
    private $mockOrderRepo;

    /**
     * Configura a classe de teste antes do teste.
     */
    protected function setUp(): void
    {
        // Definir variáveis de ambiente para o teste.
        $_ENV['API_ENDPOINT'] = 'http://api.mock.com/';
        $_ENV['API_TOKEN'] = 'mock-token';

        // Mocar todas as dependências que o construtor exige.
        $this->mockPaymentHandle = $this->createMock(PaymentHandle::class);
        $this->mockDataDTO = $this->createMock(DataDTO::class);
        $this->mockResponseDTO = $this->createMock(ResponseDTO::class);
        $this->mockUtilityRepo = $this->createMock(UtilityRepository::class);
        $this->mockOrderRepo = $this->createMock(OrderRepository::class);
    }

    /**
     * @test
     * Testa o método processarPagamentos em um cenário de sucesso.
     */
    public function testProcessarPagamentosComSucesso(): void
    {
        $payloadDeTeste = [
            'order_id' => 123,
            'amount' => 100,
        ];

        $dataGerada = ['external_order_id' => 'abc-123'];
        $respostaMockedDaApi = '{"Error":false,"Transaction_code":"00","Message":"Transacao Aprovada"}';
        $handlerResult = ['id' => 'abc-123', 'situacao' => 2];
        $finalResponseDTO = ['status' => 'success', 'data' => $handlerResult];

        $serviceMock = $this->getMockBuilder(PaymentService::class)
            ->setConstructorArgs([
                $this->mockPaymentHandle,
                $this->mockDataDTO,
                $this->mockResponseDTO,
                $this->mockUtilityRepo,
                $this->mockOrderRepo
            ])
            ->onlyMethods(['validatePayload', 'chamadaAPI'])
            ->getMock();

        $serviceMock->method('validatePayload')
            ->with($payloadDeTeste)
            ->willReturn($dataGerada);

        $serviceMock->method('chamadaAPI')
            ->with($dataGerada)
            ->willReturn($respostaMockedDaApi);

        $this->mockPaymentHandle->method('handle')
            ->with(
                $this->isInstanceOf(stdClass::class),
                $dataGerada['external_order_id']
            )
            ->willReturn($handlerResult);

        $this->mockResponseDTO->method('getResponseDTO')
            ->with($handlerResult)
            ->willReturn($finalResponseDTO);

        $result = $serviceMock->processarPagamentos($payloadDeTeste);
        $this->assertEquals($finalResponseDTO, $result);
    }
}


