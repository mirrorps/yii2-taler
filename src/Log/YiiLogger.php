<?php

namespace mirrorps\Yii2Taler\Log;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Stringable;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\log\Logger;

/**
 * Bridges PSR-3 log records from taler-php into Yii's logging subsystem.
 */
class YiiLogger extends BaseObject implements LoggerInterface
{
    use LoggerTrait;

    public string $category = 'yii2-taler';

    /**
     * @var array<string, int>
     */
    private const LEVEL_MAP = [
        LogLevel::EMERGENCY => Logger::LEVEL_ERROR,
        LogLevel::ALERT => Logger::LEVEL_ERROR,
        LogLevel::CRITICAL => Logger::LEVEL_ERROR,
        LogLevel::ERROR => Logger::LEVEL_ERROR,
        LogLevel::WARNING => Logger::LEVEL_WARNING,
        LogLevel::NOTICE => Logger::LEVEL_INFO,
        LogLevel::INFO => Logger::LEVEL_INFO,
        LogLevel::DEBUG => Logger::LEVEL_TRACE,
    ];

    /**
     * @param mixed[] $context
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $level = (string) $level;

        if (!isset(self::LEVEL_MAP[$level])) {
            throw new InvalidArgumentException(sprintf('Unsupported PSR-3 log level "%s".', $level));
        }

        Yii::getLogger()->log(
            $this->formatMessage((string) $message, $context),
            self::LEVEL_MAP[$level],
            $this->category
        );
    }

    /**
     * @param mixed[] $context
     */
    private function formatMessage(string $message, array $context): string
    {
        $interpolated = $this->interpolate($message, $context);

        if ($context === []) {
            return $interpolated;
        }

        return $interpolated . ' ' . $this->encodeContext($context);
    }

    /**
     * @param mixed[] $context
     */
    private function interpolate(string $message, array &$context): string
    {
        $replace = [];

        foreach ($context as $key => $value) {
            $placeholder = '{' . $key . '}';

            if ((is_scalar($value) || $value === null || $value instanceof Stringable) && str_contains($message, $placeholder)) {
                $replace[$placeholder] = (string) $value;
                unset($context[$key]);
            }
        }

        return $replace === [] ? $message : strtr($message, $replace);
    }

    /**
     * @param mixed[] $context
     */
    private function encodeContext(array $context): string
    {
        try {
            return Json::encode($context);
        } catch (\Throwable) {
            return VarDumper::export($context);
        }
    }
}
