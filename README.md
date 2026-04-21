# yii2-taler

> **Notice:** This package is under active development, and the code may change.

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

## Config API

The Config API is accessible via `Yii::$app->taler->configs()`.

### Get Merchant Config

```php
use Taler\Api\Config\Dto\MerchantVersionResponse;

$config = Yii::$app->taler->configs()->getConfig();

if ($config instanceof MerchantVersionResponse) {
    echo $config->name . PHP_EOL;      // taler-merchant
    echo $config->version . PHP_EOL;   // libtool format current:revision:age
    echo $config->currency . PHP_EOL;  // default currency

    foreach ($config->currencies as $code => $spec) {
        echo $code . ' => ' . $spec->name . PHP_EOL;
    }

    foreach ($config->exchanges as $exchange) {
        echo $exchange->base_url . PHP_EOL;
    }
}
```

### Async Config Call

```php
$promise = Yii::$app->taler->configs()->getConfigAsync();
$config = $promise->wait();
```

### Credential Health Check


```php
$report = Yii::$app->taler->configCheck();

if ($report['ok']) {
    echo "All checks passed" . PHP_EOL;
} else {
    print_r($report);
}
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

## Instance API

The Instance API is accessible via `Yii::$app->taler->instances()`.

### List Instances

```php
$list = Yii::$app->taler->instances()->getInstances();

foreach ($list->instances as $instance) {
    echo $instance->id . ' — ' . $instance->name . PHP_EOL;
}
```

> NOTE: 
> If your backend returns `404`, you are likely using a per-instance base URL such as `https://backend.demo.taler.net/instances/sandbox`.
> In that setup, use the single-instance private endpoint instead:
> `GET https://backend.demo.taler.net/instances/sandbox/private`


### Get Instance Details

```php
$details = Yii::$app->taler->instances()->getInstance('shop-1');

echo $details->name;         // e.g., "My Shop"
echo $details->merchant_pub; // EddsaPublicKey
```

### Create Instance

```php
use Taler\Api\Instance\Dto\InstanceConfigurationMessage;
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;
use Taler\Api\Dto\Location;
use Taler\Api\Dto\RelativeTime;

$config = new InstanceConfigurationMessage(
    id: 'shop-1',
    name: 'My Shop',
    auth: new InstanceAuthConfigToken(password: 'super-secret'),
    address: new Location(country: 'DE', town: 'Berlin'),
    jurisdiction: new Location(country: 'DE', town: 'Berlin'),
    use_stefan: true,
    default_wire_transfer_delay: new RelativeTime(3600_000_000),
    default_pay_delay: new RelativeTime(300_000_000),
    email: 'merchant@example.com',
);

Yii::$app->taler->instances()->createInstance($config);
```

### Update Instance

```php
use Taler\Api\Instance\Dto\InstanceReconfigurationMessage;
use Taler\Api\Dto\Location;
use Taler\Api\Dto\RelativeTime;

$patch = new InstanceReconfigurationMessage(
    name: 'My Shop GmbH',
    address: new Location(country: 'DE', town: 'Berlin'),
    jurisdiction: new Location(country: 'DE', town: 'Berlin'),
    use_stefan: true,
    default_wire_transfer_delay: new RelativeTime(7200_000_000),
    default_pay_delay: new RelativeTime(600_000_000),
    website: 'https://shop.example.com',
);

Yii::$app->taler->instances()->updateInstance('shop-1', $patch);
```

### Delete Instance

```php
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

// Disable instance
$result = Yii::$app->taler->instances()->deleteInstance('shop-1');

if ($result instanceof ChallengeResponse) {
    echo '2FA required. Challenge ID: ' . $result->getChallengeId() . PHP_EOL;
}

// Purge instance (irreversible)
Yii::$app->taler->instances()->deleteInstance('shop-1', purge: true);
```

### Authentication & Access Tokens

```php
use Taler\Api\Instance\Dto\LoginTokenRequest;
use Taler\Api\Instance\Dto\GetAccessTokensRequest;
use Taler\Api\Dto\RelativeTime;

// Request a login token
$token = Yii::$app->taler->instances()->getAccessToken(
    'shop-1',
    new LoginTokenRequest(
        scope: 'order-full',
        duration: new RelativeTime(3_600_000_000),
        description: 'Backoffice session',
    )
);
echo $token->access_token . PHP_EOL;

// List issued tokens
$tokens = Yii::$app->taler->instances()->getAccessTokens(
    'shop-1',
    new GetAccessTokensRequest(limit: -20)
);
if ($tokens !== null) {
    foreach ($tokens->tokens as $t) {
        echo $t->serial . ' ' . $t->scope . PHP_EOL;
    }
}

// Revoke current token
Yii::$app->taler->instances()->deleteAccessToken('shop-1');

// Revoke token by serial
Yii::$app->taler->instances()->deleteAccessTokenBySerial('shop-1', 123);
```

### Update Auth

```php
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;

$result = Yii::$app->taler->instances()->updateAuth(
    'shop-1',
    new InstanceAuthConfigToken(password: 'new-secret')
);
```

### Forgot Password

```php
use Taler\Api\Instance\Dto\InstanceAuthConfigToken;

$result = Yii::$app->taler->instances()->forgotPassword(
    'shop-1',
    new InstanceAuthConfigToken(password: 'new-password')
);
```

### KYC Status

```php
use Taler\Api\Instance\Dto\GetKycStatusRequest;

$kyc = Yii::$app->taler->instances()->getKycStatus('shop-1');

if ($kyc !== null) {
    foreach ($kyc->kyc_data as $entry) {
        echo $entry->exchange_url . PHP_EOL;
    }
}
```

### Merchant Statistics

```php
use Taler\Api\Instance\Dto\GetMerchantStatisticsAmountRequest;
use Taler\Api\Instance\Dto\GetMerchantStatisticsCounterRequest;

$amounts = Yii::$app->taler->instances()->getMerchantStatisticsAmount(
    'shop-1',
    'ORDERS',
    new GetMerchantStatisticsAmountRequest(by: 'BUCKET')
);

$counters = Yii::$app->taler->instances()->getMerchantStatisticsCounter(
    'shop-1',
    'VISITS',
    new GetMerchantStatisticsCounterRequest(by: 'INTERVAL')
);
```

## Async Support

All API methods support asynchronous execution by appending `Async` to the method name. Async methods return a promise that resolves to the same typed DTO as the synchronous variant.

```php
// Example: create an order asynchronously
$promise = Yii::$app->taler->orders()->createOrderAsync(
    new PostOrderRequest(order: $order)
);

$response = $promise->wait();
echo 'Created order (async): ' . $response->order_id . PHP_EOL;

// Example: list instances asynchronously
$promise = Yii::$app->taler->instances()->getInstancesAsync();
$list = $promise->wait();
```

## Bank Accounts API

The Bank Accounts API is accessible via `Yii::$app->taler->bankAccounts()`.

### List Bank Accounts

```php
$accounts = Yii::$app->taler->bankAccounts()->getAccounts();

foreach ($accounts->accounts as $entry) {
    echo $entry->h_wire . ' ' . $entry->payto_uri . PHP_EOL;
}
```

### Get Bank Account Details

```php
$account = Yii::$app->taler->bankAccounts()->getAccount('h_wire_hash_here');

echo $account->payto_uri . PHP_EOL;
echo $account->active ? 'active' : 'inactive';
```

### Create Bank Account

```php
use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountAddResponse;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;

$result = Yii::$app->taler->bankAccounts()->createAccount(
    new AccountAddDetails(
        payto_uri: 'payto://iban/DE75512108001245126199?receiver-name=Merchant'
    )
);

if ($result instanceof ChallengeResponse) {
    echo '2FA required: ' . $result->getChallengeId() . PHP_EOL;
} elseif ($result instanceof AccountAddResponse) {
    echo 'h_wire: ' . $result->h_wire . PHP_EOL;
}
```

### Update Bank Account

```php
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;

Yii::$app->taler->bankAccounts()->updateAccount(
    'h_wire_hash_here',
    new AccountPatchDetails(
        credit_facade_url: 'https://bank-facade.example'
    )
);
```

### Delete Bank Account

```php
Yii::$app->taler->bankAccounts()->deleteAccount('h_wire_hash_here');
```

## Testing

```bash
vendor/bin/phpunit
```

## Funding

This project is funded through [NGI TALER Fund](https://nlnet.nl/taler), a fund established by [NLnet](https://nlnet.nl) with financial support from the European Commission's [Next Generation Internet](https://ngi.eu) program. Learn more at the [NLnet project page](https://nlnet.nl/project/TalerPHP).

[<img src="https://nlnet.nl/logo/banner.png" alt="NLnet foundation logo" width="20%" />](https://nlnet.nl)
