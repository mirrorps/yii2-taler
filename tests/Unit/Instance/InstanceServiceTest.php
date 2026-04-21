<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Instance;

use mirrorps\Yii2Taler\Instance\InstanceService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Instance\Dto\GetAccessTokensRequest;
use Taler\Api\Instance\Dto\GetKycStatusRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsAmountRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsCounterRequest;
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;
use Taler\Api\Instance\Dto\InstanceConfigurationMessage;
use Taler\Api\Instance\Dto\InstanceReconfigurationMessage;
use Taler\Api\Instance\Dto\InstancesResponse;
use Taler\Api\Instance\Dto\LoginTokenRequest;
use Taler\Api\Instance\Dto\LoginTokenSuccessResponse;
use Taler\Api\Instance\Dto\MerchantAccountKycRedirectsResponse;
use Taler\Api\Instance\Dto\MerchantStatisticsAmountResponse;
use Taler\Api\Instance\Dto\MerchantStatisticsCounterResponse;
use Taler\Api\Instance\Dto\QueryInstancesResponse;
use Taler\Api\Instance\Dto\TokenInfos;
use Taler\Api\Instance\InstanceClient;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use Taler\Taler as TalerClient;

class InstanceServiceTest extends TestCase
{
    private Taler $taler;
    private InstanceClient $instanceClient;
    private InstanceService $service;

    protected function setUp(): void
    {
        $this->instanceClient = $this->createMock(InstanceClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('instance')->willReturn($this->instanceClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new InstanceService($this->taler);
    }

    public function testTalerInstancesReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->instances();
        $second = $taler->instances();

        $this->assertSame($first, $second);
    }

    public function testTalerInstancesReturnsInstanceService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(InstanceService::class, $taler->instances());
    }

    public function testCreateInstanceDelegatesToClient(): void
    {
        $config = $this->createMock(InstanceConfigurationMessage::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('createInstance')
            ->with($config, []);

        $this->service->createInstance($config);
    }

    public function testCreateInstancePassesHeaders(): void
    {
        $config = $this->createMock(InstanceConfigurationMessage::class);
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient
            ->expects($this->once())
            ->method('createInstance')
            ->with($config, $headers);

        $this->service->createInstance($config, $headers);
    }

    public function testCreateInstanceAsyncDelegatesToClient(): void
    {
        $config = $this->createMock(InstanceConfigurationMessage::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('createInstanceAsync')
            ->with($config, [])
            ->willReturn('promise');

        $result = $this->service->createInstanceAsync($config);

        $this->assertSame('promise', $result);
    }

    public function testForgotPasswordDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('forgotPassword')
            ->with('shop-1', $authConfig, [])
            ->willReturn(null);

        $result = $this->service->forgotPassword('shop-1', $authConfig);

        $this->assertNull($result);
    }

    public function testForgotPasswordReturnsChallengeResponse(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);
        $challenge = $this->createMock(ChallengeResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('forgotPassword')
            ->with('shop-1', $authConfig, [])
            ->willReturn($challenge);

        $result = $this->service->forgotPassword('shop-1', $authConfig);

        $this->assertSame($challenge, $result);
    }

    public function testForgotPasswordAsyncDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('forgotPasswordAsync')
            ->with('shop-1', $authConfig, [])
            ->willReturn('promise');

        $result = $this->service->forgotPasswordAsync('shop-1', $authConfig);

        $this->assertSame('promise', $result);
    }

    public function testUpdateAuthDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('updateAuth')
            ->with('shop-1', $authConfig, [])
            ->willReturn(null);

        $result = $this->service->updateAuth('shop-1', $authConfig);

        $this->assertNull($result);
    }

    public function testUpdateAuthPassesHeaders(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);
        $headers = ['Taler-Challenge-Ids' => 'abc123'];

        $this->instanceClient
            ->expects($this->once())
            ->method('updateAuth')
            ->with('shop-1', $authConfig, $headers)
            ->willReturn(null);

        $this->service->updateAuth('shop-1', $authConfig, $headers);
    }

    public function testUpdateAuthAsyncDelegatesToClient(): void
    {
        $authConfig = $this->createMock(InstanceAuthConfigToken::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('updateAuthAsync')
            ->with('shop-1', $authConfig, [])
            ->willReturn('promise');

        $result = $this->service->updateAuthAsync('shop-1', $authConfig);

        $this->assertSame('promise', $result);
    }

    public function testGetAccessTokenDelegatesToClient(): void
    {
        $loginRequest = $this->createMock(LoginTokenRequest::class);
        $tokenResponse = $this->createMock(LoginTokenSuccessResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getAccessToken')
            ->with('shop-1', $loginRequest, [])
            ->willReturn($tokenResponse);

        $result = $this->service->getAccessToken('shop-1', $loginRequest);

        $this->assertSame($tokenResponse, $result);
    }

    public function testGetAccessTokenPassesHeaders(): void
    {
        $loginRequest = $this->createMock(LoginTokenRequest::class);
        $tokenResponse = $this->createMock(LoginTokenSuccessResponse::class);
        $headers = ['Authorization' => 'Bearer test'];

        $this->instanceClient
            ->expects($this->once())
            ->method('getAccessToken')
            ->with('shop-1', $loginRequest, $headers)
            ->willReturn($tokenResponse);

        $this->service->getAccessToken('shop-1', $loginRequest, $headers);
    }

    public function testGetAccessTokenAsyncDelegatesToClient(): void
    {
        $loginRequest = $this->createMock(LoginTokenRequest::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getAccessTokenAsync')
            ->with('shop-1', $loginRequest, [])
            ->willReturn('promise');

        $result = $this->service->getAccessTokenAsync('shop-1', $loginRequest);

        $this->assertSame('promise', $result);
    }

    public function testGetAccessTokensDelegatesToClient(): void
    {
        $tokenInfos = $this->createMock(TokenInfos::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getAccessTokens')
            ->with('shop-1', null, [])
            ->willReturn($tokenInfos);

        $result = $this->service->getAccessTokens('shop-1');

        $this->assertSame($tokenInfos, $result);
    }

    public function testGetAccessTokensWithRequest(): void
    {
        $request = $this->createMock(GetAccessTokensRequest::class);
        $tokenInfos = $this->createMock(TokenInfos::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getAccessTokens')
            ->with('shop-1', $request, [])
            ->willReturn($tokenInfos);

        $result = $this->service->getAccessTokens('shop-1', $request);

        $this->assertSame($tokenInfos, $result);
    }

    public function testGetAccessTokensReturnsNull(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getAccessTokens')
            ->with('shop-1', null, [])
            ->willReturn(null);

        $result = $this->service->getAccessTokens('shop-1');

        $this->assertNull($result);
    }

    public function testGetAccessTokensAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getAccessTokensAsync')
            ->with('shop-1', null, [])
            ->willReturn('promise');

        $result = $this->service->getAccessTokensAsync('shop-1');

        $this->assertSame('promise', $result);
    }

    public function testDeleteAccessTokenDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('deleteAccessToken')
            ->with('shop-1', []);

        $this->service->deleteAccessToken('shop-1');
    }

    public function testDeleteAccessTokenAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('deleteAccessTokenAsync')
            ->with('shop-1', [])
            ->willReturn('promise');

        $result = $this->service->deleteAccessTokenAsync('shop-1');

        $this->assertSame('promise', $result);
    }

    public function testDeleteAccessTokenBySerialDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('deleteAccessTokenBySerial')
            ->with('shop-1', 42, []);

        $this->service->deleteAccessTokenBySerial('shop-1', 42);
    }

    public function testDeleteAccessTokenBySerialPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient
            ->expects($this->once())
            ->method('deleteAccessTokenBySerial')
            ->with('shop-1', 42, $headers);

        $this->service->deleteAccessTokenBySerial('shop-1', 42, $headers);
    }

    public function testDeleteAccessTokenBySerialAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('deleteAccessTokenBySerialAsync')
            ->with('shop-1', 42, [])
            ->willReturn('promise');

        $result = $this->service->deleteAccessTokenBySerialAsync('shop-1', 42);

        $this->assertSame('promise', $result);
    }

    public function testUpdateInstanceDelegatesToClient(): void
    {
        $message = $this->createMock(InstanceReconfigurationMessage::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('updateInstance')
            ->with('shop-1', $message, []);

        $this->service->updateInstance('shop-1', $message);
    }

    public function testUpdateInstancePassesHeaders(): void
    {
        $message = $this->createMock(InstanceReconfigurationMessage::class);
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient
            ->expects($this->once())
            ->method('updateInstance')
            ->with('shop-1', $message, $headers);

        $this->service->updateInstance('shop-1', $message, $headers);
    }

    public function testUpdateInstanceAsyncDelegatesToClient(): void
    {
        $message = $this->createMock(InstanceReconfigurationMessage::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('updateInstanceAsync')
            ->with('shop-1', $message, [])
            ->willReturn('promise');

        $result = $this->service->updateInstanceAsync('shop-1', $message);

        $this->assertSame('promise', $result);
    }

    public function testGetInstancesDelegatesToClient(): void
    {
        $response = $this->createMock(InstancesResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getInstances')
            ->with([])
            ->willReturn($response);

        $result = $this->service->getInstances();

        $this->assertSame($response, $result);
    }

    public function testGetInstancesPassesHeaders(): void
    {
        $response = $this->createMock(InstancesResponse::class);
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient
            ->expects($this->once())
            ->method('getInstances')
            ->with($headers)
            ->willReturn($response);

        $this->service->getInstances($headers);
    }

    public function testGetInstancesAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getInstancesAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getInstancesAsync();

        $this->assertSame('promise', $result);
    }

    public function testGetInstanceDelegatesToClient(): void
    {
        $response = $this->createMock(QueryInstancesResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getInstance')
            ->with('shop-1', [])
            ->willReturn($response);

        $result = $this->service->getInstance('shop-1');

        $this->assertSame($response, $result);
    }

    public function testGetInstancePassesHeaders(): void
    {
        $response = $this->createMock(QueryInstancesResponse::class);
        $headers = ['X-Custom' => 'value'];

        $this->instanceClient
            ->expects($this->once())
            ->method('getInstance')
            ->with('shop-1', $headers)
            ->willReturn($response);

        $this->service->getInstance('shop-1', $headers);
    }

    public function testGetInstanceAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getInstanceAsync')
            ->with('shop-1', [])
            ->willReturn('promise');

        $result = $this->service->getInstanceAsync('shop-1');

        $this->assertSame('promise', $result);
    }

    public function testGetKycStatusDelegatesToClient(): void
    {
        $response = $this->createMock(MerchantAccountKycRedirectsResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getKycStatus')
            ->with('shop-1', null, [])
            ->willReturn($response);

        $result = $this->service->getKycStatus('shop-1');

        $this->assertSame($response, $result);
    }

    public function testGetKycStatusWithRequest(): void
    {
        $request = $this->createMock(GetKycStatusRequest::class);
        $response = $this->createMock(MerchantAccountKycRedirectsResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getKycStatus')
            ->with('shop-1', $request, [])
            ->willReturn($response);

        $result = $this->service->getKycStatus('shop-1', $request);

        $this->assertSame($response, $result);
    }

    public function testGetKycStatusReturnsNull(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getKycStatus')
            ->with('shop-1', null, [])
            ->willReturn(null);

        $result = $this->service->getKycStatus('shop-1');

        $this->assertNull($result);
    }

    public function testGetKycStatusAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getKycStatusAsync')
            ->with('shop-1', null, [])
            ->willReturn('promise');

        $result = $this->service->getKycStatusAsync('shop-1');

        $this->assertSame('promise', $result);
    }

    public function testGetMerchantStatisticsAmountDelegatesToClient(): void
    {
        $response = $this->createMock(MerchantStatisticsAmountResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getMerchantStatisticsAmount')
            ->with('shop-1', 'ORDERS', null, [])
            ->willReturn($response);

        $result = $this->service->getMerchantStatisticsAmount('shop-1', 'ORDERS');

        $this->assertSame($response, $result);
    }

    public function testGetMerchantStatisticsAmountWithRequest(): void
    {
        $request = $this->createMock(GetMerchantStatisticsAmountRequest::class);
        $response = $this->createMock(MerchantStatisticsAmountResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getMerchantStatisticsAmount')
            ->with('shop-1', 'ORDERS', $request, [])
            ->willReturn($response);

        $result = $this->service->getMerchantStatisticsAmount('shop-1', 'ORDERS', $request);

        $this->assertSame($response, $result);
    }

    public function testGetMerchantStatisticsAmountAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getMerchantStatisticsAmountAsync')
            ->with('shop-1', 'ORDERS', null, [])
            ->willReturn('promise');

        $result = $this->service->getMerchantStatisticsAmountAsync('shop-1', 'ORDERS');

        $this->assertSame('promise', $result);
    }

    public function testGetMerchantStatisticsCounterDelegatesToClient(): void
    {
        $response = $this->createMock(MerchantStatisticsCounterResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getMerchantStatisticsCounter')
            ->with('shop-1', 'VISITS', null, [])
            ->willReturn($response);

        $result = $this->service->getMerchantStatisticsCounter('shop-1', 'VISITS');

        $this->assertSame($response, $result);
    }

    public function testGetMerchantStatisticsCounterWithRequest(): void
    {
        $request = $this->createMock(GetMerchantStatisticsCounterRequest::class);
        $response = $this->createMock(MerchantStatisticsCounterResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('getMerchantStatisticsCounter')
            ->with('shop-1', 'VISITS', $request, [])
            ->willReturn($response);

        $result = $this->service->getMerchantStatisticsCounter('shop-1', 'VISITS', $request);

        $this->assertSame($response, $result);
    }

    public function testGetMerchantStatisticsCounterAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('getMerchantStatisticsCounterAsync')
            ->with('shop-1', 'VISITS', null, [])
            ->willReturn('promise');

        $result = $this->service->getMerchantStatisticsCounterAsync('shop-1', 'VISITS');

        $this->assertSame('promise', $result);
    }

    public function testDeleteInstanceDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('deleteInstance')
            ->with('shop-1', false, [])
            ->willReturn(null);

        $result = $this->service->deleteInstance('shop-1');

        $this->assertNull($result);
    }

    public function testDeleteInstanceWithPurge(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('deleteInstance')
            ->with('shop-1', true, [])
            ->willReturn(null);

        $result = $this->service->deleteInstance('shop-1', true);

        $this->assertNull($result);
    }

    public function testDeleteInstanceReturnsChallengeResponse(): void
    {
        $challenge = $this->createMock(ChallengeResponse::class);

        $this->instanceClient
            ->expects($this->once())
            ->method('deleteInstance')
            ->with('shop-1', false, [])
            ->willReturn($challenge);

        $result = $this->service->deleteInstance('shop-1');

        $this->assertSame($challenge, $result);
    }

    public function testDeleteInstancePassesHeaders(): void
    {
        $headers = ['Taler-Challenge-Ids' => 'abc123'];

        $this->instanceClient
            ->expects($this->once())
            ->method('deleteInstance')
            ->with('shop-1', false, $headers)
            ->willReturn(null);

        $this->service->deleteInstance('shop-1', false, $headers);
    }

    public function testDeleteInstanceAsyncDelegatesToClient(): void
    {
        $this->instanceClient
            ->expects($this->once())
            ->method('deleteInstanceAsync')
            ->with('shop-1', false, [])
            ->willReturn('promise');

        $result = $this->service->deleteInstanceAsync('shop-1');

        $this->assertSame('promise', $result);
    }

    public function testGetInstanceClientReturnsSameInstance(): void
    {
        $first = $this->service->getInstanceClient();
        $second = $this->service->getInstanceClient();

        $this->assertSame($first, $second);
    }

    public function testGetInstanceClientReturnsInstanceClient(): void
    {
        $this->assertInstanceOf(InstanceClient::class, $this->service->getInstanceClient());
    }
}
