<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Wallet;

use mirrorps\Yii2Taler\Taler;
use mirrorps\Yii2Taler\Wallet\WalletService;
use PHPUnit\Framework\TestCase;
use Taler\Api\Wallet\Dto\StatusUnpaidResponse;
use Taler\Api\Wallet\WalletClient;
use Taler\Taler as TalerClient;

class WalletServiceTest extends TestCase
{
    private Taler $taler;
    private WalletClient $walletClient;
    private WalletService $service;

    protected function setUp(): void
    {
        $this->walletClient = $this->createMock(WalletClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('wallet')->willReturn($this->walletClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new WalletService($this->taler);
    }

    public function testTalerWalletsReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->wallets();
        $second = $taler->wallets();

        $this->assertSame($first, $second);
    }

    public function testTalerWalletsReturnsWalletService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(WalletService::class, $taler->wallets());
    }

    public function testGetOrderDelegatesToClient(): void
    {
        $status = $this->createMock(StatusUnpaidResponse::class);
        $params = ['session_id' => 'sess-123'];

        $this->walletClient
            ->expects($this->once())
            ->method('getOrder')
            ->with('order-123', $params, [])
            ->willReturn($status);

        $result = $this->service->getOrder('order-123', $params);

        $this->assertSame($status, $result);
    }

    public function testGetOrderPassesHeaders(): void
    {
        $status = $this->createMock(StatusUnpaidResponse::class);
        $headers = ['X-Custom' => 'value'];

        $this->walletClient
            ->expects($this->once())
            ->method('getOrder')
            ->with('order-123', [], $headers)
            ->willReturn($status);

        $this->service->getOrder('order-123', [], $headers);
    }

    public function testGetOrderAsyncDelegatesToClient(): void
    {
        $this->walletClient
            ->expects($this->once())
            ->method('getOrderAsync')
            ->with('order-321', [], [])
            ->willReturn('promise');

        $result = $this->service->getOrderAsync('order-321');

        $this->assertSame('promise', $result);
    }

    public function testGetWalletClientReturnsSameInstance(): void
    {
        $first = $this->service->getWalletClient();
        $second = $this->service->getWalletClient();

        $this->assertSame($first, $second);
    }

    public function testGetWalletClientReturnsWalletClient(): void
    {
        $this->assertInstanceOf(WalletClient::class, $this->service->getWalletClient());
    }
}
