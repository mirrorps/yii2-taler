<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Log;

use mirrorps\Yii2Taler\Log\YiiLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Yii;
use yii\log\Logger;

class YiiLoggerTest extends TestCase
{
    private Logger $yiiLogger;

    protected function setUp(): void
    {
        $this->yiiLogger = new Logger();
        Yii::setLogger($this->yiiLogger);
    }

    protected function tearDown(): void
    {
        Yii::setLogger(null);
    }

    public function testDebugMessagesAreLoggedAsYiiTrace(): void
    {
        $logger = new YiiLogger(['category' => 'taler-api']);

        $logger->debug('Taler request: https://example.com/config, GET');

        $this->assertCount(1, $this->yiiLogger->messages);
        $this->assertSame('Taler request: https://example.com/config, GET', $this->yiiLogger->messages[0][0]);
        $this->assertSame(Logger::LEVEL_TRACE, $this->yiiLogger->messages[0][1]);
        $this->assertSame('taler-api', $this->yiiLogger->messages[0][2]);
    }

    public function testContextIsSerializedWithoutDuplicatingInterpolatedValues(): void
    {
        $logger = new YiiLogger();

        $logger->warning('Protocol version {version}', [
            'version' => '1:0:0',
            'server' => 'merchant',
        ]);

        $this->assertSame(
            'Protocol version 1:0:0 {"server":"merchant"}',
            $this->yiiLogger->messages[0][0]
        );
        $this->assertSame(Logger::LEVEL_WARNING, $this->yiiLogger->messages[0][1]);
    }

    public function testArrayContextIsPreservedForTalerHeaderLogs(): void
    {
        $logger = new YiiLogger();

        $logger->debug('Taler request headers: ', [
            'Authorization' => ['[redacted]'],
        ]);

        $this->assertSame(
            'Taler request headers:  {"Authorization":["[redacted]"]}',
            $this->yiiLogger->messages[0][0]
        );
    }

    public function testInvalidLevelThrowsPsrException(): void
    {
        $logger = new YiiLogger();

        $this->expectException(InvalidArgumentException::class);

        $logger->log('verbose', 'Unsupported level');
    }
}
