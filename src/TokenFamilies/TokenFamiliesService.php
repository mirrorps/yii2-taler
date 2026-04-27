<?php

namespace mirrorps\Yii2Taler\TokenFamilies;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\TokenFamilies\Dto\TokenFamiliesList;
use Taler\Api\TokenFamilies\Dto\TokenFamilyCreateRequest;
use Taler\Api\TokenFamilies\Dto\TokenFamilyDetails;
use Taler\Api\TokenFamilies\Dto\TokenFamilyUpdateRequest;
use Taler\Api\TokenFamilies\TokenFamiliesClient;
use yii\base\Component;

/**
 * TokenFamiliesService - Yii2 service component for GNU Taler Token Families API.
 *
 * Provides a thin, testable wrapper over the underlying TokenFamiliesClient.
 */
class TokenFamiliesService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var TokenFamiliesClient|null Lazily resolved TokenFamiliesClient */
    private ?TokenFamiliesClient $_tokenFamiliesClient = null;

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
     * Returns the underlying TokenFamiliesClient, creating it on first access.
     */
    public function getTokenFamiliesClient(): TokenFamiliesClient
    {
        if ($this->_tokenFamiliesClient === null) {
            $this->_tokenFamiliesClient = $this->_taler->getClient()->tokenFamilies();
        }

        return $this->_tokenFamiliesClient;
    }

    /**
     * Create token family.
     *
     * @param TokenFamilyCreateRequest $request
     * @param array<string, string> $headers
     * @return void
     */
    public function createTokenFamily(TokenFamilyCreateRequest $request, array $headers = []): void
    {
        $this->getTokenFamiliesClient()->createTokenFamily($request, $headers);
    }

    /**
     * Create token family asynchronously.
     *
     * @param TokenFamilyCreateRequest $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createTokenFamilyAsync(TokenFamilyCreateRequest $request, array $headers = []): mixed
    {
        return $this->getTokenFamiliesClient()->createTokenFamilyAsync($request, $headers);
    }

    /**
     * Update token family.
     *
     * @param string $slug
     * @param TokenFamilyUpdateRequest $request
     * @param array<string, string> $headers
     * @return void
     */
    public function updateTokenFamily(string $slug, TokenFamilyUpdateRequest $request, array $headers = []): void
    {
        $this->getTokenFamiliesClient()->updateTokenFamily($slug, $request, $headers);
    }

    /**
     * Update token family asynchronously.
     *
     * @param string $slug
     * @param TokenFamilyUpdateRequest $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function updateTokenFamilyAsync(string $slug, TokenFamilyUpdateRequest $request, array $headers = []): mixed
    {
        return $this->getTokenFamiliesClient()->updateTokenFamilyAsync($slug, $request, $headers);
    }

    /**
     * List token families.
     *
     * @param array<string, string> $headers
     * @return TokenFamiliesList|array<string, mixed>
     */
    public function getTokenFamilies(array $headers = []): TokenFamiliesList|array
    {
        return $this->getTokenFamiliesClient()->getTokenFamilies($headers);
    }

    /**
     * List token families asynchronously.
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getTokenFamiliesAsync(array $headers = []): mixed
    {
        return $this->getTokenFamiliesClient()->getTokenFamiliesAsync($headers);
    }

    /**
     * Get token family details.
     *
     * @param string $slug
     * @param array<string, string> $headers
     * @return TokenFamilyDetails|array<string, mixed>
     */
    public function getTokenFamily(string $slug, array $headers = []): TokenFamilyDetails|array
    {
        return $this->getTokenFamiliesClient()->getTokenFamily($slug, $headers);
    }

    /**
     * Get token family details asynchronously.
     *
     * @param string $slug
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getTokenFamilyAsync(string $slug, array $headers = []): mixed
    {
        return $this->getTokenFamiliesClient()->getTokenFamilyAsync($slug, $headers);
    }

    /**
     * Delete token family.
     *
     * @param string $slug
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteTokenFamily(string $slug, array $headers = []): void
    {
        $this->getTokenFamiliesClient()->deleteTokenFamily($slug, $headers);
    }

    /**
     * Delete token family asynchronously.
     *
     * @param string $slug
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteTokenFamilyAsync(string $slug, array $headers = []): mixed
    {
        return $this->getTokenFamiliesClient()->deleteTokenFamilyAsync($slug, $headers);
    }
}
