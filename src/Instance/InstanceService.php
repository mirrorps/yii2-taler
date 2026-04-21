<?php

namespace mirrorps\Yii2Taler\Instance;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\Instance\Dto\GetAccessTokensRequest;
use Taler\Api\Instance\Dto\GetKycStatusRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsAmountRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsCounterRequest;
use Taler\Api\Instance\Dto\InstanceAuthConfigExternal;
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;
use Taler\Api\Instance\Dto\InstanceAuthConfigTokenOLD;
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
use yii\base\Component;

/**
 * InstanceService — Yii2 service component for the GNU Taler Instance API.
 *
 * Provides a clean, Yii2-idiomatic interface over the underlying
 * {@see InstanceClient} from the `taler-php` package.
 *
 * Usage (via Taler component):
 *
 * ```php
 * $instances = Yii::$app->taler->instances()->getInstances();
 * ```
 */
class InstanceService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var InstanceClient|null Lazily resolved InstanceClient */
    private ?InstanceClient $_instanceClient = null;

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
     * Returns the underlying InstanceClient, creating it on first access.
     *
     * @return InstanceClient
     */
    public function getInstanceClient(): InstanceClient
    {
        if ($this->_instanceClient === null) {
            $this->_instanceClient = $this->_taler->getClient()->instance();
        }

        return $this->_instanceClient;
    }

    // ── Create Instance ─────────────────────────────────────────────────

    /**
     * Create a new merchant instance.
     *
     * @param InstanceConfigurationMessage $instanceConfiguration
     * @param array<string, string> $headers
     * @return void
     */
    public function createInstance(InstanceConfigurationMessage $instanceConfiguration, array $headers = []): void
    {
        $this->getInstanceClient()->createInstance($instanceConfiguration, $headers);
    }

    /**
     * Create a new merchant instance (async).
     *
     * @param InstanceConfigurationMessage $instanceConfiguration
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createInstanceAsync(InstanceConfigurationMessage $instanceConfiguration, array $headers = []): mixed
    {
        return $this->getInstanceClient()->createInstanceAsync($instanceConfiguration, $headers);
    }

    // ── Forgot Password ─────────────────────────────────────────────────

    /**
     * Reset the password for a merchant instance.
     *
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     * @return ChallengeResponse|null
     */
    public function forgotPassword(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): ?ChallengeResponse {
        return $this->getInstanceClient()->forgotPassword($instanceId, $authConfig, $headers);
    }

    /**
     * Reset the password for a merchant instance (async).
     *
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     * @return mixed
     */
    public function forgotPasswordAsync(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->forgotPasswordAsync($instanceId, $authConfig, $headers);
    }

    // ── Update Auth ─────────────────────────────────────────────────────

    /**
     * Update the authentication settings for a merchant instance.
     *
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     * @return ChallengeResponse|null
     */
    public function updateAuth(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): ?ChallengeResponse {
        return $this->getInstanceClient()->updateAuth($instanceId, $authConfig, $headers);
    }

    /**
     * Update the authentication settings for a merchant instance (async).
     *
     * @param string $instanceId
     * @param InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig
     * @param array<string, string> $headers
     * @return mixed
     */
    public function updateAuthAsync(
        string $instanceId,
        InstanceAuthConfigToken|InstanceAuthConfigTokenOLD|InstanceAuthConfigExternal $authConfig,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->updateAuthAsync($instanceId, $authConfig, $headers);
    }

    // ── Get Access Token ────────────────────────────────────────────────

    /**
     * Retrieve an access token for the merchant API.
     *
     * @param string $instanceId
     * @param LoginTokenRequest $loginTokenRequest
     * @param array<string, string> $headers
     * @return LoginTokenSuccessResponse|ChallengeResponse|array
     */
    public function getAccessToken(
        string $instanceId,
        LoginTokenRequest $loginTokenRequest,
        array $headers = []
    ): LoginTokenSuccessResponse|ChallengeResponse|array {
        return $this->getInstanceClient()->getAccessToken($instanceId, $loginTokenRequest, $headers);
    }

    /**
     * Retrieve an access token for the merchant API (async).
     *
     * @param string $instanceId
     * @param LoginTokenRequest $loginTokenRequest
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getAccessTokenAsync(
        string $instanceId,
        LoginTokenRequest $loginTokenRequest,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->getAccessTokenAsync($instanceId, $loginTokenRequest, $headers);
    }

    // ── Get Access Tokens ───────────────────────────────────────────────

    /**
     * Retrieve a list of issued access tokens.
     *
     * @param string $instanceId
     * @param GetAccessTokensRequest|null $request
     * @param array<string, string> $headers
     * @return TokenInfos|array|null
     */
    public function getAccessTokens(
        string $instanceId,
        ?GetAccessTokensRequest $request = null,
        array $headers = []
    ): TokenInfos|array|null {
        return $this->getInstanceClient()->getAccessTokens($instanceId, $request, $headers);
    }

    /**
     * Retrieve a list of issued access tokens (async).
     *
     * @param string $instanceId
     * @param GetAccessTokensRequest|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getAccessTokensAsync(
        string $instanceId,
        ?GetAccessTokensRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->getAccessTokensAsync($instanceId, $request, $headers);
    }

    // ── Delete Access Token ─────────────────────────────────────────────

    /**
     * Delete the token presented in the authorization header.
     *
     * @param string $instanceId
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteAccessToken(string $instanceId, array $headers = []): void
    {
        $this->getInstanceClient()->deleteAccessToken($instanceId, $headers);
    }

    /**
     * Delete the token presented in the authorization header (async).
     *
     * @param string $instanceId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteAccessTokenAsync(string $instanceId, array $headers = []): mixed
    {
        return $this->getInstanceClient()->deleteAccessTokenAsync($instanceId, $headers);
    }

    // ── Delete Access Token By Serial ───────────────────────────────────

    /**
     * Delete a token by its serial number.
     *
     * @param string $instanceId
     * @param int $serial
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteAccessTokenBySerial(string $instanceId, int $serial, array $headers = []): void
    {
        $this->getInstanceClient()->deleteAccessTokenBySerial($instanceId, $serial, $headers);
    }

    /**
     * Delete a token by its serial number (async).
     *
     * @param string $instanceId
     * @param int $serial
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteAccessTokenBySerialAsync(string $instanceId, int $serial, array $headers = []): mixed
    {
        return $this->getInstanceClient()->deleteAccessTokenBySerialAsync($instanceId, $serial, $headers);
    }

    // ── Update Instance ─────────────────────────────────────────────────

    /**
     * Update the configuration of a merchant instance.
     *
     * @param string $instanceId
     * @param InstanceReconfigurationMessage $message
     * @param array<string, string> $headers
     * @return void
     */
    public function updateInstance(
        string $instanceId,
        InstanceReconfigurationMessage $message,
        array $headers = []
    ): void {
        $this->getInstanceClient()->updateInstance($instanceId, $message, $headers);
    }

    /**
     * Update the configuration of a merchant instance (async).
     *
     * @param string $instanceId
     * @param InstanceReconfigurationMessage $message
     * @param array<string, string> $headers
     * @return mixed
     */
    public function updateInstanceAsync(
        string $instanceId,
        InstanceReconfigurationMessage $message,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->updateInstanceAsync($instanceId, $message, $headers);
    }

    // ── Get Instances ───────────────────────────────────────────────────

    /**
     * Retrieve the list of all merchant instances (admin only).
     *
     * @param array<string, string> $headers
     * @return InstancesResponse|array
     */
    public function getInstances(array $headers = []): InstancesResponse|array
    {
        return $this->getInstanceClient()->getInstances($headers);
    }

    /**
     * Retrieve the list of all merchant instances (async).
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getInstancesAsync(array $headers = []): mixed
    {
        return $this->getInstanceClient()->getInstancesAsync($headers);
    }

    // ── Get Instance ────────────────────────────────────────────────────

    /**
     * Query a specific merchant instance.
     *
     * @param string $instanceId
     * @param array<string, string> $headers
     * @return QueryInstancesResponse|array
     */
    public function getInstance(string $instanceId, array $headers = []): QueryInstancesResponse|array
    {
        return $this->getInstanceClient()->getInstance($instanceId, $headers);
    }

    /**
     * Query a specific merchant instance (async).
     *
     * @param string $instanceId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getInstanceAsync(string $instanceId, array $headers = []): mixed
    {
        return $this->getInstanceClient()->getInstanceAsync($instanceId, $headers);
    }

    // ── KYC Status ──────────────────────────────────────────────────────

    /**
     * Check KYC status of a particular payment target.
     *
     * @param string $instanceId
     * @param GetKycStatusRequest|null $request
     * @param array<string, string> $headers
     * @return MerchantAccountKycRedirectsResponse|array|null
     */
    public function getKycStatus(
        string $instanceId,
        ?GetKycStatusRequest $request = null,
        array $headers = []
    ): MerchantAccountKycRedirectsResponse|array|null {
        return $this->getInstanceClient()->getKycStatus($instanceId, $request, $headers);
    }

    /**
     * Check KYC status of a particular payment target (async).
     *
     * @param string $instanceId
     * @param GetKycStatusRequest|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getKycStatusAsync(
        string $instanceId,
        ?GetKycStatusRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->getKycStatusAsync($instanceId, $request, $headers);
    }

    // ── Merchant Statistics (Amount) ────────────────────────────────────

    /**
     * Retrieve merchant statistics where values are amounts.
     *
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsAmountRequest|null $request
     * @param array<string, string> $headers
     * @return MerchantStatisticsAmountResponse|array
     */
    public function getMerchantStatisticsAmount(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsAmountRequest $request = null,
        array $headers = []
    ): MerchantStatisticsAmountResponse|array {
        return $this->getInstanceClient()->getMerchantStatisticsAmount($instanceId, $slug, $request, $headers);
    }

    /**
     * Retrieve merchant statistics where values are amounts (async).
     *
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsAmountRequest|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getMerchantStatisticsAmountAsync(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsAmountRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->getMerchantStatisticsAmountAsync($instanceId, $slug, $request, $headers);
    }

    // ── Merchant Statistics (Counter) ───────────────────────────────────

    /**
     * Retrieve merchant statistics where values are counters.
     *
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsCounterRequest|null $request
     * @param array<string, string> $headers
     * @return MerchantStatisticsCounterResponse|array
     */
    public function getMerchantStatisticsCounter(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsCounterRequest $request = null,
        array $headers = []
    ): MerchantStatisticsCounterResponse|array {
        return $this->getInstanceClient()->getMerchantStatisticsCounter($instanceId, $slug, $request, $headers);
    }

    /**
     * Retrieve merchant statistics where values are counters (async).
     *
     * @param string $instanceId
     * @param string $slug
     * @param GetMerchantStatisticsCounterRequest|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getMerchantStatisticsCounterAsync(
        string $instanceId,
        string $slug,
        ?GetMerchantStatisticsCounterRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->getInstanceClient()->getMerchantStatisticsCounterAsync($instanceId, $slug, $request, $headers);
    }

    // ── Delete Instance ─────────────────────────────────────────────────

    /**
     * Delete (disable) or purge a merchant instance.
     *
     * @param string $instanceId
     * @param bool $purge
     * @param array<string, string> $headers
     * @return ChallengeResponse|null
     */
    public function deleteInstance(string $instanceId, bool $purge = false, array $headers = []): ?ChallengeResponse
    {
        return $this->getInstanceClient()->deleteInstance($instanceId, $purge, $headers);
    }

    /**
     * Delete (disable) or purge a merchant instance (async).
     *
     * @param string $instanceId
     * @param bool $purge
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteInstanceAsync(string $instanceId, bool $purge = false, array $headers = []): mixed
    {
        return $this->getInstanceClient()->deleteInstanceAsync($instanceId, $purge, $headers);
    }
}
