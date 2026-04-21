<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Config;

use mirrorps\Yii2Taler\Config\ConfigService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Config\ConfigClient;
use Taler\Api\Config\Dto\MerchantVersionResponse;
use Taler\Taler as TalerClient;

class ConfigServiceTest extends TestCase
{
    private Taler $taler;
    private ConfigClient $configClient;
    private ConfigService $service;

    protected function setUp(): void
    {
        $this->configClient = $this->createMock(ConfigClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('configApi')->willReturn($this->configClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new ConfigService($this->taler);
    }

    public function testTalerConfigsReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->configs();
        $second = $taler->configs();

        $this->assertSame($first, $second);
    }

    public function testTalerConfigsReturnsConfigService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(ConfigService::class, $taler->configs());
    }

    public function testGetConfigDelegatesToClient(): void
    {
        $response = $this->createMock(MerchantVersionResponse::class);

        $this->configClient
            ->expects($this->once())
            ->method('getConfig')
            ->with([])
            ->willReturn($response);

        $result = $this->service->getConfig();

        $this->assertSame($response, $result);
    }

    public function testGetConfigPassesHeaders(): void
    {
        $response = $this->createMock(MerchantVersionResponse::class);
        $headers = ['X-Custom-Header' => 'value'];

        $this->configClient
            ->expects($this->once())
            ->method('getConfig')
            ->with($headers)
            ->willReturn($response);

        $this->service->getConfig($headers);
    }

    public function testGetConfigAsyncDelegatesToClient(): void
    {
        $this->configClient
            ->expects($this->once())
            ->method('getConfigAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getConfigAsync();

        $this->assertSame('promise', $result);
    }

    public function testGetConfigClientReturnsSameInstance(): void
    {
        $first = $this->service->getConfigClient();
        $second = $this->service->getConfigClient();

        $this->assertSame($first, $second);
    }

    public function testGetConfigClientReturnsConfigClient(): void
    {
        $this->assertInstanceOf(ConfigClient::class, $this->service->getConfigClient());
    }
}
