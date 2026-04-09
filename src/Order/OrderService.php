<?php

namespace mirrorps\Yii2Taler\Order;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\Order\Dto\CheckPaymentClaimedResponse;
use Taler\Api\Order\Dto\CheckPaymentPaidResponse;
use Taler\Api\Order\Dto\CheckPaymentUnpaidResponse;
use Taler\Api\Order\Dto\ForgetRequest;
use Taler\Api\Order\Dto\GetOrderRequest;
use Taler\Api\Order\Dto\GetOrdersRequest;
use Taler\Api\Order\Dto\MerchantRefundResponse;
use Taler\Api\Order\Dto\OrderHistory;
use Taler\Api\Order\Dto\PostOrderRequest;
use Taler\Api\Order\Dto\PostOrderResponse;
use Taler\Api\Order\Dto\RefundRequest;
use Taler\Api\Order\OrderClient;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * OrderService — Yii2 service component for the GNU Taler Order API.
 *
 * Provides a clean, Yii2-idiomatic interface over the underlying
 * {@see OrderClient} from the `taler-php` package.
 *
 * Usage (via Taler component):
 *
 * ```php
 * $orders = Yii::$app->taler->orders()->getOrders();
 * ```
 */
class OrderService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var OrderClient|null Lazily resolved OrderClient */
    private ?OrderClient $_orderClient = null;

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
     * Returns the underlying OrderClient, creating it on first access.
     *
     * @return OrderClient
     */
    public function getOrderClient(): OrderClient
    {
        if ($this->_orderClient === null) {
            $this->_orderClient = $this->_taler->getClient()->order();
        }

        return $this->_orderClient;
    }

    /**
     * Retrieve order history.
     *
     * @param GetOrdersRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     * @return OrderHistory|array
     */
    public function getOrders(GetOrdersRequest|array|null $request = null, array $headers = []): OrderHistory|array
    {
        return $this->getOrderClient()->getOrders($request, $headers);
    }

    /**
     * Retrieve order history (async).
     *
     * @param GetOrdersRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getOrdersAsync(GetOrdersRequest|array|null $request = null, array $headers = []): mixed
    {
        return $this->getOrderClient()->getOrdersAsync($request, $headers);
    }

    /**
     * Retrieve a specific order's payment status.
     *
     * @param string $orderId
     * @param GetOrderRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     * @return CheckPaymentPaidResponse|CheckPaymentClaimedResponse|CheckPaymentUnpaidResponse|ChallengeResponse|array
     */
    public function getOrder(
        string $orderId,
        GetOrderRequest|array|null $request = null,
        array $headers = []
    ): CheckPaymentPaidResponse|CheckPaymentClaimedResponse|CheckPaymentUnpaidResponse|ChallengeResponse|array {
        return $this->getOrderClient()->getOrder($orderId, $request, $headers);
    }

    /**
     * Retrieve a specific order's payment status (async).
     *
     * @param string $orderId
     * @param GetOrderRequest|array<string, scalar>|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getOrderAsync(
        string $orderId,
        GetOrderRequest|array|null $request = null,
        array $headers = []
    ): mixed {
        return $this->getOrderClient()->getOrderAsync($orderId, $request, $headers);
    }

    /**
     * Create a new order.
     *
     * @param PostOrderRequest $postOrderRequest
     * @param array<string, string> $headers
     * @return PostOrderResponse|array
     */
    public function createOrder(PostOrderRequest $postOrderRequest, array $headers = []): PostOrderResponse|array
    {
        return $this->getOrderClient()->createOrder($postOrderRequest, $headers);
    }

    /**
     * Create a new order (async).
     *
     * @param PostOrderRequest $postOrderRequest
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createOrderAsync(PostOrderRequest $postOrderRequest, array $headers = []): mixed
    {
        return $this->getOrderClient()->createOrderAsync($postOrderRequest, $headers);
    }

    /**
     * Initiate a refund for a specific order.
     *
     * @param string $orderId
     * @param RefundRequest $refundRequest
     * @param array<string, string> $headers
     * @return MerchantRefundResponse|array
     */
    public function refundOrder(
        string $orderId,
        RefundRequest $refundRequest,
        array $headers = []
    ): MerchantRefundResponse|array {
        return $this->getOrderClient()->refundOrder($orderId, $refundRequest, $headers);
    }

    /**
     * Initiate a refund for a specific order (async).
     *
     * @param string $orderId
     * @param RefundRequest $refundRequest
     * @param array<string, string> $headers
     * @return mixed
     */
    public function refundOrderAsync(
        string $orderId,
        RefundRequest $refundRequest,
        array $headers = []
    ): mixed {
        return $this->getOrderClient()->refundOrderAsync($orderId, $refundRequest, $headers);
    }

    /**
     * Delete an order.
     *
     * @param string $orderId
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteOrder(string $orderId, array $headers = []): void
    {
        $this->getOrderClient()->deleteOrder($orderId, $headers);
    }

    /**
     * Delete an order (async).
     *
     * @param string $orderId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteOrderAsync(string $orderId, array $headers = []): mixed
    {
        return $this->getOrderClient()->deleteOrderAsync($orderId, $headers);
    }

    /**
     * Forget specific fields of an order (GDPR compliance).
     *
     * @param string $orderId
     * @param ForgetRequest $forgetRequest
     * @param array<string, string> $headers
     * @return void
     */
    public function forgetOrder(string $orderId, ForgetRequest $forgetRequest, array $headers = []): void
    {
        $this->getOrderClient()->forgetOrder($orderId, $forgetRequest, $headers);
    }

    /**
     * Forget specific fields of an order (async).
     *
     * @param string $orderId
     * @param ForgetRequest $forgetRequest
     * @param array<string, string> $headers
     * @return mixed
     */
    public function forgetOrderAsync(string $orderId, ForgetRequest $forgetRequest, array $headers = []): mixed
    {
        return $this->getOrderClient()->forgetOrderAsync($orderId, $forgetRequest, $headers);
    }
}
