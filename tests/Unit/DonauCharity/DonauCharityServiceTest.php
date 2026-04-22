<?php

namespace mirrorps\Yii2Taler\Tests\Unit\DonauCharity;

use mirrorps\Yii2Taler\DonauCharity\DonauCharityService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\DonauCharity\DonauCharityClient;
use Taler\Api\DonauCharity\Dto\DonauInstancesResponse;
use Taler\Api\DonauCharity\Dto\PostDonauRequest;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use Taler\Taler as TalerClient;

class DonauCharityServiceTest extends TestCase
{
    private Taler $taler;
    private DonauCharityClient $donauCharityClient;
    private DonauCharityService $service;

    protected function setUp(): void
    {
        $this->donauCharityClient = $this->createMock(DonauCharityClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('donauCharity')->willReturn($this->donauCharityClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new DonauCharityService($this->taler);
    }

    public function testTalerDonauCharitiesReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->donauCharities();
        $second = $taler->donauCharities();

        $this->assertSame($first, $second);
    }

    public function testTalerDonauCharitiesReturnsDonauCharityService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(DonauCharityService::class, $taler->donauCharities());
    }

    public function testGetInstancesDelegatesToClient(): void
    {
        $response = $this->createMock(DonauInstancesResponse::class);

        $this->donauCharityClient
            ->expects($this->once())
            ->method('getInstances')
            ->with([])
            ->willReturn($response);

        $result = $this->service->getInstances();

        $this->assertSame($response, $result);
    }

    public function testGetInstancesPassesHeaders(): void
    {
        $response = $this->createMock(DonauInstancesResponse::class);
        $headers = ['X-Custom-Header' => 'value'];

        $this->donauCharityClient
            ->expects($this->once())
            ->method('getInstances')
            ->with($headers)
            ->willReturn($response);

        $this->service->getInstances($headers);
    }

    public function testGetInstancesAsyncDelegatesToClient(): void
    {
        $this->donauCharityClient
            ->expects($this->once())
            ->method('getInstancesAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getInstancesAsync();

        $this->assertSame('promise', $result);
    }

    public function testCreateDonauCharityDelegatesToClient(): void
    {
        $request = new PostDonauRequest('https://donau.example', 7);

        $this->donauCharityClient
            ->expects($this->once())
            ->method('createDonauCharity')
            ->with($request, [])
            ->willReturn(null);

        $result = $this->service->createDonauCharity($request);

        $this->assertNull($result);
    }

    public function testCreateDonauCharityReturnsChallengeResponse(): void
    {
        $request = new PostDonauRequest('https://donau.example', 7);
        $response = $this->createMock(ChallengeResponse::class);

        $this->donauCharityClient
            ->expects($this->once())
            ->method('createDonauCharity')
            ->with($request, [])
            ->willReturn($response);

        $result = $this->service->createDonauCharity($request);

        $this->assertSame($response, $result);
    }

    public function testCreateDonauCharityPassesHeaders(): void
    {
        $request = new PostDonauRequest('https://donau.example', 7);
        $headers = ['Taler-Challenge-Ids' => 'abc123'];

        $this->donauCharityClient
            ->expects($this->once())
            ->method('createDonauCharity')
            ->with($request, $headers)
            ->willReturn(null);

        $this->service->createDonauCharity($request, $headers);
    }

    public function testCreateDonauCharityAsyncDelegatesToClient(): void
    {
        $request = new PostDonauRequest('https://donau.example', 7);

        $this->donauCharityClient
            ->expects($this->once())
            ->method('createDonauCharityAsync')
            ->with($request, [])
            ->willReturn('promise');

        $result = $this->service->createDonauCharityAsync($request);

        $this->assertSame('promise', $result);
    }

    public function testDeleteDonauCharityBySerialDelegatesToClient(): void
    {
        $this->donauCharityClient
            ->expects($this->once())
            ->method('deleteDonauCharityBySerial')
            ->with(42, []);

        $this->service->deleteDonauCharityBySerial(42);
    }

    public function testDeleteDonauCharityBySerialPassesHeaders(): void
    {
        $headers = ['X-Custom-Header' => 'value'];

        $this->donauCharityClient
            ->expects($this->once())
            ->method('deleteDonauCharityBySerial')
            ->with(42, $headers);

        $this->service->deleteDonauCharityBySerial(42, $headers);
    }

    public function testDeleteDonauCharityBySerialAsyncDelegatesToClient(): void
    {
        $this->donauCharityClient
            ->expects($this->once())
            ->method('deleteDonauCharityBySerialAsync')
            ->with(42, [])
            ->willReturn('promise');

        $result = $this->service->deleteDonauCharityBySerialAsync(42);

        $this->assertSame('promise', $result);
    }

    public function testGetDonauCharityClientReturnsSameInstance(): void
    {
        $first = $this->service->getDonauCharityClient();
        $second = $this->service->getDonauCharityClient();

        $this->assertSame($first, $second);
    }

    public function testGetDonauCharityClientReturnsDonauCharityClient(): void
    {
        $this->assertInstanceOf(DonauCharityClient::class, $this->service->getDonauCharityClient());
    }
}
