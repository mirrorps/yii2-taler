<?php

namespace mirrorps\Yii2Taler;

use mirrorps\Yii2Taler\Order\OrderService;
use Taler\Factory\Factory;
use Taler\Taler as TalerClient;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Taler Yii2 component
 *
 * Application configuration example:
 *
 * ```php
 * 'components' => [
 *     'taler' => [
 *         'class'    => \mirrorps\Yii2Taler\Taler::class,
 *         'baseUrl'  => 'https://backend.demo.taler.net/instances/sandbox',
 *         'token'    => 'Bearer secret-token',
 *         // -- OR credential-based auth --
 *         // 'username' => 'merchant',
 *         // 'password' => 'secret',
 *         // 'instance' => 'instance-id',
 *     ],
 * ],
 * ```
 */
class Taler extends Component
{
    /** @var string Required: Taler merchant backend base URL */
    public string $baseUrl = '';

    /** @var string|null Bearer token for direct token authentication */
    public ?string $token = null;

    /** @var string|null Username for credential-based authentication */
    public ?string $username = null;

    /** @var string|null Password for credential-based authentication */
    public ?string $password = null;

    /** @var string|null Merchant instance identifier */
    public ?string $instance = null;

    /** @var string|null OAuth scope */
    public ?string $scope = null;

    /** @var int|null Token duration in microseconds */
    public ?int $durationUs = null;

    /** @var string|null Token description */
    public ?string $description = null;

    /** @var bool Wrap responses in typed DTOs */
    public bool $wrapResponse = true;

    /** @var bool Enable debug logging */
    public bool $debugLoggingEnabled = false;

    private ?TalerClient $_client = null;

    private ?OrderService $_orderService = null;

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if ($this->baseUrl === '') {
            throw new InvalidConfigException(static::class . '::$baseUrl must be set.');
        }
    }

    /**
     * Returns the underlying TalerClient, creating it on first access.
     */
    public function getClient(): TalerClient
    {
        if ($this->_client === null) {
            $this->_client = Factory::create($this->buildOptions());
        }

        return $this->_client;
    }

    /**
     * Returns the Order API service.
     *
     * @return OrderService
     */
    public function orders(): OrderService
    {
        if ($this->_orderService === null) {
            $this->_orderService = new OrderService($this);
        }

        return $this->_orderService;
    }

    /**
     * Proxy to TalerClient — delegates all unknown calls to the underlying client.
     *
     * @param string $name
     * @param array  $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        $client = $this->getClient();

        if (method_exists($client, $name)) {
            return $client->$name(...$params);
        }

        return parent::__call($name, $params);
    }

    private function buildOptions(): array
    {
        $options = [
            'base_url'            => $this->baseUrl,
            'wrapResponse'        => $this->wrapResponse,
            'debugLoggingEnabled' => $this->debugLoggingEnabled,
        ];

        if ($this->token !== null) {
            $options['token'] = $this->token;
        }

        if ($this->username !== null) {
            $options['username'] = $this->username;
        }

        if ($this->password !== null) {
            $options['password'] = $this->password;
        }

        if ($this->instance !== null) {
            $options['instance'] = $this->instance;
        }

        if ($this->scope !== null) {
            $options['scope'] = $this->scope;
        }

        if ($this->durationUs !== null) {
            $options['duration_us'] = $this->durationUs;
        }

        if ($this->description !== null) {
            $options['description'] = $this->description;
        }

        return $options;
    }
}
