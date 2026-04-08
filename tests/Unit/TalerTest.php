<?php

namespace mirrorps\Yii2Taler\Tests\Unit;

use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;

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
}
