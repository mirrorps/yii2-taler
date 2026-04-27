<?php

namespace mirrorps\Yii2Taler\Tests\Unit\TwoFactorAuth;

use mirrorps\Yii2Taler\Taler;
use mirrorps\Yii2Taler\TwoFactorAuth\TwoFactorAuthService;
use PHPUnit\Framework\TestCase;
use Taler\Api\TwoFactorAuth\Dto\ChallengeRequestResponse;
use Taler\Api\TwoFactorAuth\Dto\MerchantChallengeSolveRequest;
use Taler\Api\TwoFactorAuth\TwoFactorAuthClient;
use Taler\Taler as TalerClient;

class TwoFactorAuthServiceTest extends TestCase
{
    private Taler $taler;
    private TwoFactorAuthClient $twoFactorAuthClient;
    private TwoFactorAuthService $service;

    protected function setUp(): void
    {
        $this->twoFactorAuthClient = $this->createMock(TwoFactorAuthClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('twoFactorAuth')->willReturn($this->twoFactorAuthClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new TwoFactorAuthService($this->taler);
    }

    public function testTalerTwoFactorAuthReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->twoFactorAuth();
        $second = $taler->twoFactorAuth();

        $this->assertSame($first, $second);
    }

    public function testTalerTwoFactorAuthReturnsService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(TwoFactorAuthService::class, $taler->twoFactorAuth());
    }

    public function testRequestChallengeDelegatesToClient(): void
    {
        $requestBody = ['resend' => true];
        $response = $this->createMock(ChallengeRequestResponse::class);

        $this->twoFactorAuthClient
            ->expects($this->once())
            ->method('requestChallenge')
            ->with('sandbox', 'challenge-1', $requestBody, [])
            ->willReturn($response);

        $result = $this->service->requestChallenge('sandbox', 'challenge-1', $requestBody);

        $this->assertSame($response, $result);
    }

    public function testRequestChallengeAsyncDelegatesToClient(): void
    {
        $requestBody = ['resend' => true];

        $this->twoFactorAuthClient
            ->expects($this->once())
            ->method('requestChallengeAsync')
            ->with('sandbox', 'challenge-2', $requestBody, [])
            ->willReturn('promise');

        $result = $this->service->requestChallengeAsync('sandbox', 'challenge-2', $requestBody);

        $this->assertSame('promise', $result);
    }

    public function testConfirmChallengeDelegatesToClient(): void
    {
        $solveRequest = new MerchantChallengeSolveRequest(tan: '123456');

        $this->twoFactorAuthClient
            ->expects($this->once())
            ->method('confirmChallenge')
            ->with('sandbox', 'challenge-3', $solveRequest, []);

        $this->service->confirmChallenge('sandbox', 'challenge-3', $solveRequest);
    }

    public function testConfirmChallengeAsyncDelegatesToClient(): void
    {
        $solveRequest = new MerchantChallengeSolveRequest(tan: '654321');

        $this->twoFactorAuthClient
            ->expects($this->once())
            ->method('confirmChallengeAsync')
            ->with('sandbox', 'challenge-4', $solveRequest, [])
            ->willReturn('promise');

        $result = $this->service->confirmChallengeAsync('sandbox', 'challenge-4', $solveRequest);

        $this->assertSame('promise', $result);
    }

    public function testGetTwoFactorAuthClientReturnsSameInstance(): void
    {
        $first = $this->service->getTwoFactorAuthClient();
        $second = $this->service->getTwoFactorAuthClient();

        $this->assertSame($first, $second);
    }

    public function testGetTwoFactorAuthClientReturnsTwoFactorAuthClient(): void
    {
        $this->assertInstanceOf(TwoFactorAuthClient::class, $this->service->getTwoFactorAuthClient());
    }
}
