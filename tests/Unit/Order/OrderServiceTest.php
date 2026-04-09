<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Order;

use mirrorps\Yii2Taler\Order\OrderService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Order\Dto\ForgetRequest;
use Taler\Api\Order\Dto\GetOrderRequest;
use Taler\Api\Order\Dto\GetOrdersRequest;
use Taler\Api\Order\Dto\MerchantRefundResponse;
use Taler\Api\Order\Dto\OrderHistory;
use Taler\Api\Order\Dto\CheckPaymentUnpaidResponse;
use Taler\Api\Order\Dto\PostOrderRequest;
use Taler\Api\Order\Dto\PostOrderResponse;
use Taler\Api\Order\Dto\RefundRequest;
use Taler\Api\Order\OrderClient;
use Taler\Taler as TalerClient;

class OrderServiceTest extends TestCase
{
    private Taler $taler;
    private OrderClient $orderClient;
    private OrderService $service;

    protected function setUp(): void
    {
        $this->orderClient = $this->createMock(OrderClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('order')->willReturn($this->orderClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new OrderService($this->taler);
    }

    // ── orders() accessor on Taler ──────────────────────────────────────

    public function testTalerOrdersReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->orders();
        $second = $taler->orders();

        $this->assertSame($first, $second);
    }

    public function testTalerOrdersReturnsOrderService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(OrderService::class, $taler->orders());
    }

    // ── getOrders ───────────────────────────────────────────────────────

    public function testGetOrdersDelegatesToClient(): void
    {
        $history = $this->createMock(OrderHistory::class);
        $request = new GetOrdersRequest(limit: 5);

        $this->orderClient
            ->expects($this->once())
            ->method('getOrders')
            ->with($request, [])
            ->willReturn($history);

        $result = $this->service->getOrders($request);

        $this->assertSame($history, $result);
    }

    public function testGetOrdersWithNullRequest(): void
    {
        $history = $this->createMock(OrderHistory::class);

        $this->orderClient
            ->expects($this->once())
            ->method('getOrders')
            ->with(null, [])
            ->willReturn($history);

        $result = $this->service->getOrders();

        $this->assertSame($history, $result);
    }

    public function testGetOrdersPassesHeaders(): void
    {
        $headers = ['X-Custom' => 'value'];
        $history = $this->createMock(OrderHistory::class);

        $this->orderClient
            ->expects($this->once())
            ->method('getOrders')
            ->with(null, $headers)
            ->willReturn($history);

        $this->service->getOrders(null, $headers);
    }

    public function testGetOrdersAsyncDelegatesToClient(): void
    {
        $this->orderClient
            ->expects($this->once())
            ->method('getOrdersAsync')
            ->with(null, [])
            ->willReturn('promise');

        $result = $this->service->getOrdersAsync();

        $this->assertSame('promise', $result);
    }

    // ── getOrder ────────────────────────────────────────────────────────

    public function testGetOrderDelegatesToClient(): void
    {
        $unpaid = $this->createMock(CheckPaymentUnpaidResponse::class);

        $this->orderClient
            ->expects($this->once())
            ->method('getOrder')
            ->with('order-123', null, [])
            ->willReturn($unpaid);

        $result = $this->service->getOrder('order-123');

        $this->assertSame($unpaid, $result);
    }

    public function testGetOrderWithRequestDto(): void
    {
        $request = new GetOrderRequest(session_id: 'sess-1');
        $unpaid = $this->createMock(CheckPaymentUnpaidResponse::class);

        $this->orderClient
            ->expects($this->once())
            ->method('getOrder')
            ->with('order-456', $request, [])
            ->willReturn($unpaid);

        $this->service->getOrder('order-456', $request);
    }

    public function testGetOrderAsyncDelegatesToClient(): void
    {
        $this->orderClient
            ->expects($this->once())
            ->method('getOrderAsync')
            ->with('order-789', null, [])
            ->willReturn('promise');

        $result = $this->service->getOrderAsync('order-789');

        $this->assertSame('promise', $result);
    }

    // ── createOrder ─────────────────────────────────────────────────────

    public function testCreateOrderDelegatesToClient(): void
    {
        $postOrderRequest = $this->createMock(PostOrderRequest::class);
        $postOrderResponse = $this->createMock(PostOrderResponse::class);

        $this->orderClient
            ->expects($this->once())
            ->method('createOrder')
            ->with($postOrderRequest, [])
            ->willReturn($postOrderResponse);

        $result = $this->service->createOrder($postOrderRequest);

        $this->assertSame($postOrderResponse, $result);
    }

    public function testCreateOrderPassesHeaders(): void
    {
        $postOrderRequest = $this->createMock(PostOrderRequest::class);
        $postOrderResponse = $this->createMock(PostOrderResponse::class);
        $headers = ['Authorization' => 'Bearer test'];

        $this->orderClient
            ->expects($this->once())
            ->method('createOrder')
            ->with($postOrderRequest, $headers)
            ->willReturn($postOrderResponse);

        $this->service->createOrder($postOrderRequest, $headers);
    }

    public function testCreateOrderAsyncDelegatesToClient(): void
    {
        $postOrderRequest = $this->createMock(PostOrderRequest::class);

        $this->orderClient
            ->expects($this->once())
            ->method('createOrderAsync')
            ->with($postOrderRequest, [])
            ->willReturn('promise');

        $result = $this->service->createOrderAsync($postOrderRequest);

        $this->assertSame('promise', $result);
    }

    // ── refundOrder ─────────────────────────────────────────────────────

    public function testRefundOrderDelegatesToClient(): void
    {
        $refundRequest = new RefundRequest(refund: 'KUDOS:1.00', reason: 'Defective');
        $refundResponse = MerchantRefundResponse::createFromArray([
            'taler_refund_uri' => 'taler://refund/example',
            'h_contract'       => 'abc123',
        ]);

        $this->orderClient
            ->expects($this->once())
            ->method('refundOrder')
            ->with('order-123', $refundRequest, [])
            ->willReturn($refundResponse);

        $result = $this->service->refundOrder('order-123', $refundRequest);

        $this->assertSame($refundResponse, $result);
    }

    public function testRefundOrderAsyncDelegatesToClient(): void
    {
        $refundRequest = new RefundRequest(refund: 'KUDOS:1.00', reason: 'Defective');

        $this->orderClient
            ->expects($this->once())
            ->method('refundOrderAsync')
            ->with('order-123', $refundRequest, [])
            ->willReturn('promise');

        $result = $this->service->refundOrderAsync('order-123', $refundRequest);

        $this->assertSame('promise', $result);
    }

    // ── deleteOrder ─────────────────────────────────────────────────────

    public function testDeleteOrderDelegatesToClient(): void
    {
        $this->orderClient
            ->expects($this->once())
            ->method('deleteOrder')
            ->with('order-123', []);

        $this->service->deleteOrder('order-123');
    }

    public function testDeleteOrderAsyncDelegatesToClient(): void
    {
        $this->orderClient
            ->expects($this->once())
            ->method('deleteOrderAsync')
            ->with('order-123', [])
            ->willReturn('promise');

        $result = $this->service->deleteOrderAsync('order-123');

        $this->assertSame('promise', $result);
    }

    // ── forgetOrder ─────────────────────────────────────────────────────

    public function testForgetOrderDelegatesToClient(): void
    {
        $forgetRequest = new ForgetRequest(fields: ['$.customer_name']);

        $this->orderClient
            ->expects($this->once())
            ->method('forgetOrder')
            ->with('order-123', $forgetRequest, []);

        $this->service->forgetOrder('order-123', $forgetRequest);
    }

    public function testForgetOrderAsyncDelegatesToClient(): void
    {
        $forgetRequest = new ForgetRequest(fields: ['$.customer_name']);

        $this->orderClient
            ->expects($this->once())
            ->method('forgetOrderAsync')
            ->with('order-123', $forgetRequest, [])
            ->willReturn('promise');

        $result = $this->service->forgetOrderAsync('order-123', $forgetRequest);

        $this->assertSame('promise', $result);
    }

    // ── getOrderClient ──────────────────────────────────────────────────

    public function testGetOrderClientReturnsSameInstance(): void
    {
        $first = $this->service->getOrderClient();
        $second = $this->service->getOrderClient();

        $this->assertSame($first, $second);
    }

    public function testGetOrderClientReturnsOrderClient(): void
    {
        $this->assertInstanceOf(OrderClient::class, $this->service->getOrderClient());
    }
}
