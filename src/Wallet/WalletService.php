<?php

namespace mirrorps\Yii2Taler\Wallet;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\Wallet\Dto\StatusGotoResponse;
use Taler\Api\Wallet\Dto\StatusPaidResponse;
use Taler\Api\Wallet\Dto\StatusUnpaidResponse;
use Taler\Api\Wallet\WalletClient;
use yii\base\Component;

/**
 * WalletService - Yii2 service component for GNU Taler Wallet API.
 *
 * Provides a thin, testable wrapper over the underlying WalletClient.
 */
class WalletService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var WalletClient|null Lazily resolved WalletClient */
    private ?WalletClient $_walletClient = null;

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
     * Returns the underlying WalletClient, creating it on first access.
     */
    public function getWalletClient(): WalletClient
    {
        if ($this->_walletClient === null) {
            $this->_walletClient = $this->_taler->getClient()->wallet();
        }

        return $this->_walletClient;
    }

    /**
     * Retrieve a public order status suitable for wallet flows.
     *
     * @param string $orderId
     * @param array<string, string> $params
     * @param array<string, string> $headers
     * @return StatusPaidResponse|StatusGotoResponse|StatusUnpaidResponse|array<string, mixed>
     */
    public function getOrder(
        string $orderId,
        array $params = [],
        array $headers = []
    ): StatusPaidResponse|StatusGotoResponse|StatusUnpaidResponse|array {
        return $this->getWalletClient()->getOrder($orderId, $params, $headers);
    }

    /**
     * Retrieve a public order status asynchronously.
     *
     * @param string $orderId
     * @param array<string, string> $params
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getOrderAsync(string $orderId, array $params = [], array $headers = []): mixed
    {
        return $this->getWalletClient()->getOrderAsync($orderId, $params, $headers);
    }
}
