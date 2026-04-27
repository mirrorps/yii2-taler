<?php

namespace mirrorps\Yii2Taler\Tests\Unit\TokenFamilies;

use mirrorps\Yii2Taler\Taler;
use mirrorps\Yii2Taler\TokenFamilies\TokenFamiliesService;
use PHPUnit\Framework\TestCase;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Dto\Timestamp;
use Taler\Api\TokenFamilies\Dto\TokenFamiliesList;
use Taler\Api\TokenFamilies\Dto\TokenFamilyCreateRequest;
use Taler\Api\TokenFamilies\Dto\TokenFamilyDetails;
use Taler\Api\TokenFamilies\Dto\TokenFamilyUpdateRequest;
use Taler\Api\TokenFamilies\TokenFamiliesClient;
use Taler\Taler as TalerClient;

class TokenFamiliesServiceTest extends TestCase
{
    private Taler $taler;
    private TokenFamiliesClient $tokenFamiliesClient;
    private TokenFamiliesService $service;

    protected function setUp(): void
    {
        $this->tokenFamiliesClient = $this->createMock(TokenFamiliesClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('tokenFamilies')->willReturn($this->tokenFamiliesClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new TokenFamiliesService($this->taler);
    }

    public function testTalerTokenFamiliesReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->tokenFamilies();
        $second = $taler->tokenFamilies();

        $this->assertSame($first, $second);
    }

    public function testTalerTokenFamiliesReturnsService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(TokenFamiliesService::class, $taler->tokenFamilies());
    }

    public function testCreateTokenFamilyDelegatesToClient(): void
    {
        $request = $this->createTokenFamilyCreateRequest();

        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('createTokenFamily')
            ->with($request, []);

        $this->service->createTokenFamily($request);
    }

    public function testCreateTokenFamilyAsyncDelegatesToClient(): void
    {
        $request = $this->createTokenFamilyCreateRequest();

        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('createTokenFamilyAsync')
            ->with($request, [])
            ->willReturn('promise');

        $result = $this->service->createTokenFamilyAsync($request);

        $this->assertSame('promise', $result);
    }

    public function testUpdateTokenFamilyDelegatesToClient(): void
    {
        $request = new TokenFamilyUpdateRequest(
            name: 'Loyalty Program',
            description: 'Updated description',
            valid_after: new Timestamp(1710000000),
            valid_before: new Timestamp(1750000000),
            description_i18n: ['de' => 'Aktualisierte Beschreibung'],
            extra_data: ['trusted_domains' => ['merchant.example']]
        );

        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('updateTokenFamily')
            ->with('loyalty-token', $request, []);

        $this->service->updateTokenFamily('loyalty-token', $request);
    }

    public function testUpdateTokenFamilyAsyncDelegatesToClient(): void
    {
        $request = new TokenFamilyUpdateRequest(
            name: 'Loyalty Program',
            description: 'Updated description',
            valid_after: new Timestamp(1710000000),
            valid_before: new Timestamp(1750000000)
        );

        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('updateTokenFamilyAsync')
            ->with('loyalty-token', $request, [])
            ->willReturn('promise');

        $result = $this->service->updateTokenFamilyAsync('loyalty-token', $request);

        $this->assertSame('promise', $result);
    }

    public function testGetTokenFamiliesDelegatesToClient(): void
    {
        $list = $this->createMock(TokenFamiliesList::class);

        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('getTokenFamilies')
            ->with([])
            ->willReturn($list);

        $result = $this->service->getTokenFamilies();

        $this->assertSame($list, $result);
    }

    public function testGetTokenFamiliesAsyncDelegatesToClient(): void
    {
        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('getTokenFamiliesAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getTokenFamiliesAsync();

        $this->assertSame('promise', $result);
    }

    public function testGetTokenFamilyDelegatesToClient(): void
    {
        $details = $this->createMock(TokenFamilyDetails::class);

        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('getTokenFamily')
            ->with('loyalty-token', [])
            ->willReturn($details);

        $result = $this->service->getTokenFamily('loyalty-token');

        $this->assertSame($details, $result);
    }

    public function testGetTokenFamilyAsyncDelegatesToClient(): void
    {
        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('getTokenFamilyAsync')
            ->with('loyalty-token', [])
            ->willReturn('promise');

        $result = $this->service->getTokenFamilyAsync('loyalty-token');

        $this->assertSame('promise', $result);
    }

    public function testDeleteTokenFamilyDelegatesToClient(): void
    {
        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('deleteTokenFamily')
            ->with('loyalty-token', []);

        $this->service->deleteTokenFamily('loyalty-token');
    }

    public function testDeleteTokenFamilyAsyncDelegatesToClient(): void
    {
        $this->tokenFamiliesClient
            ->expects($this->once())
            ->method('deleteTokenFamilyAsync')
            ->with('loyalty-token', [])
            ->willReturn('promise');

        $result = $this->service->deleteTokenFamilyAsync('loyalty-token');

        $this->assertSame('promise', $result);
    }

    public function testGetTokenFamiliesClientReturnsSameInstance(): void
    {
        $first = $this->service->getTokenFamiliesClient();
        $second = $this->service->getTokenFamiliesClient();

        $this->assertSame($first, $second);
    }

    public function testGetTokenFamiliesClientReturnsTokenFamiliesClient(): void
    {
        $this->assertInstanceOf(TokenFamiliesClient::class, $this->service->getTokenFamiliesClient());
    }

    private function createTokenFamilyCreateRequest(): TokenFamilyCreateRequest
    {
        return new TokenFamilyCreateRequest(
            slug: 'loyalty-token',
            name: 'Loyalty Program',
            description: 'Discount token family for recurring buyers',
            valid_before: new Timestamp(1750000000),
            duration: new RelativeTime(86400000000),
            validity_granularity: new RelativeTime(3600000000),
            start_offset: new RelativeTime(0),
            kind: 'discount',
            description_i18n: ['de' => 'Rabatt-Tokenfamilie'],
            extra_data: ['trusted_domains' => ['merchant.example']],
            valid_after: new Timestamp(1710000000)
        );
    }
}
