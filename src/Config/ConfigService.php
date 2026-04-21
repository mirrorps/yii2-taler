<?php

namespace mirrorps\Yii2Taler\Config;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\Config\ConfigClient;
use Taler\Api\Config\Dto\MerchantVersionResponse;
use yii\base\Component;

/**
 * ConfigService - Yii2 service component for GNU Taler Config API.
 *
 * Provides a thin, testable wrapper over the underlying ConfigClient.
 */
class ConfigService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var ConfigClient|null Lazily resolved ConfigClient */
    private ?ConfigClient $_configClient = null;

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
     * Returns the underlying ConfigClient, creating it on first access.
     */
    public function getConfigClient(): ConfigClient
    {
        if ($this->_configClient === null) {
            $this->_configClient = $this->_taler->getClient()->configApi();
        }

        return $this->_configClient;
    }

    /**
     * Retrieve merchant backend configuration and protocol metadata.
     *
     * @param array<string, string> $headers
     * @return MerchantVersionResponse|array<string, mixed>
     */
    public function getConfig(array $headers = []): MerchantVersionResponse|array
    {
        return $this->getConfigClient()->getConfig($headers);
    }

    /**
     * Retrieve merchant backend configuration asynchronously.
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getConfigAsync(array $headers = []): mixed
    {
        return $this->getConfigClient()->getConfigAsync($headers);
    }
}
