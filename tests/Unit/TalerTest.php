<?php

namespace mirrorps\Yii2Taler\Tests\Unit;

use mirrorps\Yii2Taler\Config\ConfigService;
use mirrorps\Yii2Taler\DonauCharity\DonauCharityService;
use mirrorps\Yii2Taler\Instance\InstanceService;
use mirrorps\Yii2Taler\OtpDevices\OtpDevicesService;
use mirrorps\Yii2Taler\Order\OrderService;
use mirrorps\Yii2Taler\BankAccount\BankAccountService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Taler as TalerClient;
use yii\base\InvalidConfigException;
use yii\base\UnknownMethodException;

class TalerTest extends TestCase
{
    public function testThrowsWhenBaseUrlMissing(): void
    {
        $this->expectException(InvalidConfigException::class);

        $component = new Taler();
        $component->init();
    }

    public function testInitSucceedsWithBaseUrl(): void
    {
        $component = new Taler(['baseUrl' => 'https://backend.demo.taler.net/instances/sandbox']);
        $component->init();

        $this->assertSame('https://backend.demo.taler.net/instances/sandbox', $component->baseUrl);
    }

    public function testDefaultPropertyValues(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertNull($component->token);
        $this->assertNull($component->username);
        $this->assertNull($component->password);
        $this->assertNull($component->instance);
        $this->assertNull($component->scope);
        $this->assertNull($component->durationUs);
        $this->assertNull($component->description);
        $this->assertTrue($component->wrapResponse);
        $this->assertFalse($component->debugLoggingEnabled);
    }

    public function testTokenPropertyIsAssigned(): void
    {
        $component = new Taler([
            'baseUrl' => 'https://example.com',
            'token'   => 'Bearer my-secret-token',
        ]);

        $this->assertSame('Bearer my-secret-token', $component->token);
    }

    public function testCredentialPropertiesAreAssigned(): void
    {
        $component = new Taler([
            'baseUrl'  => 'https://example.com',
            'username' => 'merchant',
            'password' => 'secret',
        ]);

        $this->assertSame('merchant', $component->username);
        $this->assertSame('secret', $component->password);
    }

    public function testRemainingPropertiesAreAssigned(): void
    {
        $component = new Taler([
            'baseUrl'             => 'https://example.com',
            'instance'            => 'sandbox',
            'scope'               => 'readonly',
            'durationUs'          => 3600000000,
            'description'         => 'integration-token',
            'wrapResponse'        => false,
            'debugLoggingEnabled' => true,
        ]);

        $this->assertSame('sandbox', $component->instance);
        $this->assertSame('readonly', $component->scope);
        $this->assertSame(3600000000, $component->durationUs);
        $this->assertSame('integration-token', $component->description);
        $this->assertFalse($component->wrapResponse);
        $this->assertTrue($component->debugLoggingEnabled);
    }

    public function testConfigsReturnsConfigService(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertInstanceOf(ConfigService::class, $component->configs());
    }

    /**
     * Guards the lazy-init cache in {@see Taler::configs()}: repeated calls
     * must return the same ConfigService instance rather than a fresh one.
     */
    public function testConfigsReturnsSameInstance(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertSame($component->configs(), $component->configs());
    }

    public function testOrdersReturnsOrderService(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertInstanceOf(OrderService::class, $component->orders());
    }

    /**
     * Guards the lazy-init cache in {@see Taler::orders()}: repeated calls
     * must return the same OrderService instance rather than a fresh one.
     */
    public function testOrdersReturnsSameInstance(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertSame($component->orders(), $component->orders());
    }

    public function testInstancesReturnsInstanceService(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertInstanceOf(InstanceService::class, $component->instances());
    }

    /**
     * Guards the lazy-init cache in {@see Taler::instances()}: repeated calls
     * must return the same InstanceService instance rather than a fresh one.
     */
    public function testInstancesReturnsSameInstance(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertSame($component->instances(), $component->instances());
    }

    public function testBankAccountsReturnsBankAccountService(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertInstanceOf(BankAccountService::class, $component->bankAccounts());
    }

    /**
     * Guards the lazy-init cache in {@see Taler::bankAccounts()}: repeated calls
     * must return the same BankAccountService instance rather than a fresh one.
     */
    public function testBankAccountsReturnsSameInstance(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertSame($component->bankAccounts(), $component->bankAccounts());
    }

    public function testDonauCharitiesReturnsDonauCharityService(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertInstanceOf(DonauCharityService::class, $component->donauCharities());
    }

    /**
     * Guards the lazy-init cache in {@see Taler::donauCharities()}: repeated calls
     * must return the same DonauCharityService instance rather than a fresh one.
     */
    public function testDonauCharitiesReturnsSameInstance(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertSame($component->donauCharities(), $component->donauCharities());
    }

    public function testOtpDevicesReturnsOtpDevicesService(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertInstanceOf(OtpDevicesService::class, $component->otpDevices());
    }

    /**
     * Guards the lazy-init cache in {@see Taler::otpDevices()}: repeated calls
     * must return the same OtpDevicesService instance rather than a fresh one.
     */
    public function testOtpDevicesReturnsSameInstance(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertSame($component->otpDevices(), $component->otpDevices());
    }

    /**
     * Guards the lazy-init cache in {@see Taler::getClient()}: once the
     * underlying TalerClient has been created, subsequent calls must return
     * the same instance instead of invoking Factory::create() again.
     *
     * The client is seeded via reflection because Factory::create() performs
     * a live HTTP call against the configured baseUrl, which is out of scope
     * for a unit test.
     */
    public function testGetClientReturnsMemoizedInstance(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);
        $talerClient = $this->createMock(TalerClient::class);

        $this->setPrivateProperty($component, '_client', $talerClient);

        $this->assertSame($talerClient, $component->getClient());
        $this->assertSame($component->getClient(), $component->getClient());
    }

    public function testMagicCallDelegatesToUnderlyingClient(): void
    {
        $talerClient = $this->createMock(TalerClient::class);
        $talerClient
            ->expects($this->once())
            ->method('configCheck')
            ->willReturn(['ok' => true]);

        $component = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();
        $component->method('getClient')->willReturn($talerClient);

        $this->assertSame(['ok' => true], $component->configCheck());
    }

    public function testMagicCallThrowsUnknownMethodForUnknownName(): void
    {
        $talerClient = $this->createMock(TalerClient::class);

        $component = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();
        $component->method('getClient')->willReturn($talerClient);

        $this->expectException(UnknownMethodException::class);

        /** @phpstan-ignore-next-line */
        $component->thisMethodDoesNotExist();
    }

    public function testBuildOptionsIncludesRequiredFieldsOnly(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $options = $this->invokeBuildOptions($component);

        $this->assertSame([
            'base_url'            => 'https://example.com',
            'wrapResponse'        => true,
            'debugLoggingEnabled' => false,
        ], $options);
    }

    public function testBuildOptionsPropagatesAllConfiguredFields(): void
    {
        $component = new Taler([
            'baseUrl'             => 'https://example.com',
            'token'               => 'Bearer token',
            'username'            => 'merchant',
            'password'            => 'secret',
            'instance'            => 'sandbox',
            'scope'               => 'readonly',
            'durationUs'          => 3600000000,
            'description'         => 'integration-token',
            'wrapResponse'        => false,
            'debugLoggingEnabled' => true,
        ]);

        $options = $this->invokeBuildOptions($component);

        $this->assertSame([
            'base_url'            => 'https://example.com',
            'wrapResponse'        => false,
            'debugLoggingEnabled' => true,
            'token'               => 'Bearer token',
            'username'            => 'merchant',
            'password'            => 'secret',
            'instance'            => 'sandbox',
            'scope'               => 'readonly',
            'duration_us'         => 3600000000,
            'description'         => 'integration-token',
        ], $options);
    }

    /**
     * @return array<string, mixed>
     */
    private function invokeBuildOptions(Taler $component): array
    {
        $method = new \ReflectionMethod(Taler::class, 'buildOptions');

        /** @var array<string, mixed> $result */
        $result = $method->invoke($component);

        return $result;
    }

    private function setPrivateProperty(object $object, string $name, mixed $value): void
    {
        $property = new \ReflectionProperty($object, $name);
        $property->setValue($object, $value);
    }
}
