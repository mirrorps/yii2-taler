<?php

namespace mirrorps\Yii2Taler\TwoFactorAuth;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\TwoFactorAuth\Dto\ChallengeRequestResponse;
use Taler\Api\TwoFactorAuth\Dto\MerchantChallengeSolveRequest;
use Taler\Api\TwoFactorAuth\TwoFactorAuthClient;
use yii\base\Component;

/**
 * TwoFactorAuthService - Yii2 service component for GNU Taler Two-Factor Auth API.
 *
 * Provides a thin, testable wrapper over the underlying TwoFactorAuthClient.
 */
class TwoFactorAuthService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var TwoFactorAuthClient|null Lazily resolved TwoFactorAuthClient */
    private ?TwoFactorAuthClient $_twoFactorAuthClient = null;

    /**
     * @param Taler $taler The parent Taler component
     * @param array $config Yii2 component config
     */
    public function __construct(Taler $taler, array $config = [])
    {
        $this->_taler = $taler;
        parent::__construct($config);
    }

    /**
     * Returns the underlying TwoFactorAuthClient, creating it on first access.
     */
    public function getTwoFactorAuthClient(): TwoFactorAuthClient
    {
        if ($this->_twoFactorAuthClient === null) {
            $this->_twoFactorAuthClient = $this->_taler->getClient()->twoFactorAuth();
        }

        return $this->_twoFactorAuthClient;
    }

    /**
     * Request TAN code transmission for a challenge.
     *
     * @param string $instanceId
     * @param string $challengeId
     * @param array<string, mixed>|null $requestBody
     * @param array<string, string> $headers
     * @return ChallengeRequestResponse|array<string, mixed>
     */
    public function requestChallenge(
        string $instanceId,
        string $challengeId,
        ?array $requestBody = null,
        array $headers = []
    ): ChallengeRequestResponse|array {
        return $this->getTwoFactorAuthClient()->requestChallenge($instanceId, $challengeId, $requestBody, $headers);
    }

    /**
     * Request TAN code transmission for a challenge asynchronously.
     *
     * @param string $instanceId
     * @param string $challengeId
     * @param array<string, mixed>|null $requestBody
     * @param array<string, string> $headers
     * @return mixed
     */
    public function requestChallengeAsync(
        string $instanceId,
        string $challengeId,
        ?array $requestBody = null,
        array $headers = []
    ): mixed {
        return $this->getTwoFactorAuthClient()->requestChallengeAsync($instanceId, $challengeId, $requestBody, $headers);
    }

    /**
     * Confirm a TAN challenge.
     *
     * @param string $instanceId
     * @param string $challengeId
     * @param MerchantChallengeSolveRequest $requestBody
     * @param array<string, string> $headers
     * @return void
     */
    public function confirmChallenge(
        string $instanceId,
        string $challengeId,
        MerchantChallengeSolveRequest $requestBody,
        array $headers = []
    ): void {
        $this->getTwoFactorAuthClient()->confirmChallenge($instanceId, $challengeId, $requestBody, $headers);
    }

    /**
     * Confirm a TAN challenge asynchronously.
     *
     * @param string $instanceId
     * @param string $challengeId
     * @param MerchantChallengeSolveRequest $requestBody
     * @param array<string, string> $headers
     * @return mixed
     */
    public function confirmChallengeAsync(
        string $instanceId,
        string $challengeId,
        MerchantChallengeSolveRequest $requestBody,
        array $headers = []
    ): mixed {
        return $this->getTwoFactorAuthClient()->confirmChallengeAsync($instanceId, $challengeId, $requestBody, $headers);
    }
}
