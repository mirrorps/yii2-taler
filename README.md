# yii2-taler

Yii2 extension for GNU Taler REST API integration.

## Installation

```bash
composer require mirrorps/yii2-taler
```

## Configuration

Add the Taler component to your application configuration:

```php
'components' => [
    'taler' => [
        'class'   => \mirrorps\Yii2Taler\Taler::class,
        'baseUrl' => 'https://backend.demo.taler.net/instances/sandbox',
        'token'   => 'Bearer secret-token:sandbox',
        // OR credential-based auth:
        // 'username' => 'merchant',
        // 'password' => 'secret',
        // 'instance' => 'sandbox',
    ],
],
```

## Order API

The Order API is accessible via `Yii::$app->taler->orders()`.

### List Orders

```php
use Taler\Api\Order\Dto\GetOrdersRequest;

// List all orders (default limit)
$orderHistory = Yii::$app->taler->orders()->getOrders();

foreach ($orderHistory->orders as $entry) {
    echo $entry->order_id . ' — ' . $entry->summary . ' — ' . $entry->amount . PHP_EOL;
}

// With filters
$orderHistory = Yii::$app->taler->orders()->getOrders(
    new GetOrdersRequest(paid: true, limit: 10)
);
```

### Get Order Details

```php
use Taler\Api\Order\Dto\GetOrderRequest;
use Taler\Api\Order\Dto\CheckPaymentPaidResponse;
use Taler\Api\Order\Dto\CheckPaymentUnpaidResponse;
use Taler\Api\Order\Dto\CheckPaymentClaimedResponse;

$order = Yii::$app->taler->orders()->getOrder('my-order-id-2025-1');

if ($order instanceof CheckPaymentPaidResponse) {
    echo 'Paid! Deposit total: ' . $order->deposit_total . PHP_EOL;
} elseif ($order instanceof CheckPaymentUnpaidResponse) {
    echo 'Unpaid. Payment URI: ' . $order->taler_pay_uri . PHP_EOL;
} elseif ($order instanceof CheckPaymentClaimedResponse) {
    echo 'Claimed by wallet.' . PHP_EOL;
}
```

### Create Order

```php
use Taler\Api\Order\Dto\Amount;
use Taler\Api\Order\Dto\OrderV0;
use Taler\Api\Order\Dto\PostOrderRequest;

$order = new OrderV0(
    summary: 'T-Shirt (GNU Taler demo)',
    amount: new Amount('KUDOS:10.00'),
    fulfillment_message: 'Thank you for purchasing a T-Shirt!',
);

$response = Yii::$app->taler->orders()->createOrder(
    new PostOrderRequest(order: $order)
);

echo 'Created order: ' . $response->order_id . PHP_EOL;
if ($response->token !== null) {
    echo 'Claim token: ' . $response->token . PHP_EOL;
}
```

### Refund Order

```php
use Taler\Api\Order\Dto\RefundRequest;

$refundResponse = Yii::$app->taler->orders()->refundOrder(
    'my-order-id-2025-1',
    new RefundRequest(
        refund: 'KUDOS:5.00',
        reason: 'Customer requested refund',
    )
);

echo 'Refund URI: ' . $refundResponse->taler_refund_uri . PHP_EOL;
```

### Delete Order

```php
Yii::$app->taler->orders()->deleteOrder('my-order-id-2025-1');
echo 'Order deleted.' . PHP_EOL;
```

### Forget Order Fields

```php
use Taler\Api\Order\Dto\ForgetRequest;

Yii::$app->taler->orders()->forgetOrder(
    'my-order-id-2025-1',
    new ForgetRequest(fields: ['$.delivery_location'])
);

echo 'Fields forgotten.' . PHP_EOL;
```

## Async Support

All Order API methods support asynchronous execution by appending `Async` to the method name. Async methods return a promise that resolves to the same typed DTO as the synchronous variant.

```php
// Example: create an order asynchronously
$promise = Yii::$app->taler->orders()->createOrderAsync(
    new PostOrderRequest(order: $order)
);

$response = $promise->wait();
echo 'Created order (async): ' . $response->order_id . PHP_EOL;
```

## Testing

```bash
vendor/bin/phpunit
```

## License

MIT
