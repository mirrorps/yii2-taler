<?php

namespace mirrorps\Yii2Taler\Tests\Unit\WireTransfers;

use mirrorps\Yii2Taler\Taler;
use mirrorps\Yii2Taler\WireTransfers\WireTransfersService;
use PHPUnit\Framework\TestCase;
use Taler\Api\WireTransfers\Dto\GetTransfersRequest;
use Taler\Api\WireTransfers\Dto\TransfersList;
use Taler\Api\WireTransfers\WireTransfersClient;
use Taler\Taler as TalerClient;

class WireTransfersServiceTest extends TestCase
{
    private Taler $taler;
    private WireTransfersClient $wireTransfersClient;
    private WireTransfersService $service;

    protected function setUp(): void
    {
        $this->wireTransfersClient = $this->createMock(WireTransfersClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('wireTransfers')->willReturn($this->wireTransfersClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new WireTransfersService($this->taler);
    }

    public function testTalerWireTransfersReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->wireTransfers();
        $second = $taler->wireTransfers();

        $this->assertSame($first, $second);
    }

    public function testTalerWireTransfersReturnsService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(WireTransfersService::class, $taler->wireTransfers());
    }

    public function testGetTransfersDelegatesToClient(): void
    {
        $request = new GetTransfersRequest(limit: 5);
        $response = $this->createMock(TransfersList::class);

        $this->wireTransfersClient
            ->expects($this->once())
            ->method('getTransfers')
            ->with($request, [])
            ->willReturn($response);

        $result = $this->service->getTransfers($request);

        $this->assertSame($response, $result);
    }

    public function testGetTransfersWithNullRequest(): void
    {
        $response = $this->createMock(TransfersList::class);

        $this->wireTransfersClient
            ->expects($this->once())
            ->method('getTransfers')
            ->with(null, [])
            ->willReturn($response);

        $result = $this->service->getTransfers();

        $this->assertSame($response, $result);
    }

    public function testGetTransfersPassesHeaders(): void
    {
        $response = $this->createMock(TransfersList::class);
        $headers = ['Authorization' => 'Bearer test'];

        $this->wireTransfersClient
            ->expects($this->once())
            ->method('getTransfers')
            ->with(null, $headers)
            ->willReturn($response);

        $this->service->getTransfers(null, $headers);
    }

    public function testGetTransfersAsyncDelegatesToClient(): void
    {
        $this->wireTransfersClient
            ->expects($this->once())
            ->method('getTransfersAsync')
            ->with(null, [])
            ->willReturn('promise');

        $result = $this->service->getTransfersAsync();

        $this->assertSame('promise', $result);
    }

    public function testDeleteTransferDelegatesToClient(): void
    {
        $this->wireTransfersClient
            ->expects($this->once())
            ->method('deleteTransfer')
            ->with('42', []);

        $this->service->deleteTransfer('42');
    }

    public function testDeleteTransferAsyncDelegatesToClient(): void
    {
        $this->wireTransfersClient
            ->expects($this->once())
            ->method('deleteTransferAsync')
            ->with('42', [])
            ->willReturn('promise');

        $result = $this->service->deleteTransferAsync('42');

        $this->assertSame('promise', $result);
    }

    public function testGetWireTransfersClientReturnsSameInstance(): void
    {
        $first = $this->service->getWireTransfersClient();
        $second = $this->service->getWireTransfersClient();

        $this->assertSame($first, $second);
    }

    public function testGetWireTransfersClientReturnsWireTransfersClient(): void
    {
        $this->assertInstanceOf(WireTransfersClient::class, $this->service->getWireTransfersClient());
    }
}
