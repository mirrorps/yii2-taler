<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Webhooks;

use mirrorps\Yii2Taler\Taler;
use mirrorps\Yii2Taler\Webhooks\WebhooksService;
use PHPUnit\Framework\TestCase;
use Taler\Api\Webhooks\Dto\WebhookAddDetails;
use Taler\Api\Webhooks\Dto\WebhookDetails;
use Taler\Api\Webhooks\Dto\WebhookPatchDetails;
use Taler\Api\Webhooks\Dto\WebhookSummaryResponse;
use Taler\Api\Webhooks\WebhooksClient;
use Taler\Taler as TalerClient;

class WebhooksServiceTest extends TestCase
{
    private Taler $taler;
    private WebhooksClient $webhooksClient;
    private WebhooksService $service;

    protected function setUp(): void
    {
        $this->webhooksClient = $this->createMock(WebhooksClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('webhooks')->willReturn($this->webhooksClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new WebhooksService($this->taler);
    }

    public function testTalerWebhooksReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->webhooks();
        $second = $taler->webhooks();

        $this->assertSame($first, $second);
    }

    public function testTalerWebhooksReturnsWebhooksService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(WebhooksService::class, $taler->webhooks());
    }

    public function testCreateWebhookDelegatesToClient(): void
    {
        $details = $this->createMock(WebhookAddDetails::class);

        $this->webhooksClient
            ->expects($this->once())
            ->method('createWebhook')
            ->with($details, []);

        $this->service->createWebhook($details);
    }

    public function testCreateWebhookAsyncDelegatesToClient(): void
    {
        $details = $this->createMock(WebhookAddDetails::class);

        $this->webhooksClient
            ->expects($this->once())
            ->method('createWebhookAsync')
            ->with($details, [])
            ->willReturn('promise');

        $result = $this->service->createWebhookAsync($details);

        $this->assertSame('promise', $result);
    }

    public function testUpdateWebhookDelegatesToClient(): void
    {
        $details = $this->createMock(WebhookPatchDetails::class);

        $this->webhooksClient
            ->expects($this->once())
            ->method('updateWebhook')
            ->with('wh-1', $details, []);

        $this->service->updateWebhook('wh-1', $details);
    }

    public function testUpdateWebhookAsyncDelegatesToClient(): void
    {
        $details = $this->createMock(WebhookPatchDetails::class);

        $this->webhooksClient
            ->expects($this->once())
            ->method('updateWebhookAsync')
            ->with('wh-1', $details, [])
            ->willReturn('promise');

        $result = $this->service->updateWebhookAsync('wh-1', $details);

        $this->assertSame('promise', $result);
    }

    public function testGetWebhooksDelegatesToClient(): void
    {
        $response = $this->createMock(WebhookSummaryResponse::class);

        $this->webhooksClient
            ->expects($this->once())
            ->method('getWebhooks')
            ->with([])
            ->willReturn($response);

        $result = $this->service->getWebhooks();

        $this->assertSame($response, $result);
    }

    public function testGetWebhooksAsyncDelegatesToClient(): void
    {
        $this->webhooksClient
            ->expects($this->once())
            ->method('getWebhooksAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getWebhooksAsync();

        $this->assertSame('promise', $result);
    }

    public function testGetWebhookDelegatesToClient(): void
    {
        $response = $this->createMock(WebhookDetails::class);

        $this->webhooksClient
            ->expects($this->once())
            ->method('getWebhook')
            ->with('wh-1', [])
            ->willReturn($response);

        $result = $this->service->getWebhook('wh-1');

        $this->assertSame($response, $result);
    }

    public function testGetWebhookPassesHeaders(): void
    {
        $response = $this->createMock(WebhookDetails::class);
        $headers = ['Authorization' => 'Bearer test'];

        $this->webhooksClient
            ->expects($this->once())
            ->method('getWebhook')
            ->with('wh-1', $headers)
            ->willReturn($response);

        $this->service->getWebhook('wh-1', $headers);
    }

    public function testGetWebhookAsyncDelegatesToClient(): void
    {
        $this->webhooksClient
            ->expects($this->once())
            ->method('getWebhookAsync')
            ->with('wh-1', [])
            ->willReturn('promise');

        $result = $this->service->getWebhookAsync('wh-1');

        $this->assertSame('promise', $result);
    }

    public function testDeleteWebhookDelegatesToClient(): void
    {
        $this->webhooksClient
            ->expects($this->once())
            ->method('deleteWebhook')
            ->with('wh-1', []);

        $this->service->deleteWebhook('wh-1');
    }

    public function testDeleteWebhookAsyncDelegatesToClient(): void
    {
        $this->webhooksClient
            ->expects($this->once())
            ->method('deleteWebhookAsync')
            ->with('wh-1', [])
            ->willReturn('promise');

        $result = $this->service->deleteWebhookAsync('wh-1');

        $this->assertSame('promise', $result);
    }

    public function testGetWebhooksClientReturnsSameInstance(): void
    {
        $first = $this->service->getWebhooksClient();
        $second = $this->service->getWebhooksClient();

        $this->assertSame($first, $second);
    }

    public function testGetWebhooksClientReturnsWebhooksClient(): void
    {
        $this->assertInstanceOf(WebhooksClient::class, $this->service->getWebhooksClient());
    }
}
