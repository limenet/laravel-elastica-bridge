<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Logging;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Sentry\Breadcrumb;
use Stringable;

class SentryBreadcrumbLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * The PSR-3 levels from the most to the least severe.
     */
    private const array LEVELS = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];

    /**
     * @param  string  $minimumLevel  a PSR-3 level; messages less severe than it are dropped
     */
    public function __construct(private readonly string $minimumLevel = LogLevel::DEBUG)
    {
        if (! in_array($minimumLevel, self::LEVELS, true)) {
            throw new InvalidArgumentException(sprintf('Unknown log level "%s".', $minimumLevel));
        }
    }

    /**
     * @param  LogLevel::*|mixed  $level
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if (! $this->isLogged($level)) {
            return;
        }

        if ($message instanceof Stringable) {
            $message = $message->__toString();
        }

        \Sentry\addBreadcrumb(
            new Breadcrumb(
                match ($level) {
                    LogLevel::EMERGENCY, LogLevel::CRITICAL => Breadcrumb::LEVEL_FATAL,
                    LogLevel::ALERT,  LogLevel::ERROR => Breadcrumb::LEVEL_ERROR,
                    LogLevel::WARNING => Breadcrumb::LEVEL_WARNING,
                    LogLevel::INFO, LogLevel::NOTICE => Breadcrumb::LEVEL_INFO,
                    LogLevel::DEBUG => Breadcrumb::LEVEL_DEBUG,
                    default => Breadcrumb::LEVEL_DEBUG,
                },
                match ($level) {
                    Breadcrumb::LEVEL_FATAL, Breadcrumb::LEVEL_ERROR => Breadcrumb::TYPE_ERROR,
                    default => Breadcrumb::TYPE_DEFAULT,
                },
                'elastica',
                $message,
                $context
            )
        );
    }

    /**
     * A level which is not one of the PSR-3 ones is always logged.
     */
    private function isLogged(mixed $level): bool
    {
        $severity = array_search($level, self::LEVELS, true);

        return $severity === false || $severity <= array_search($this->minimumLevel, self::LEVELS, true);
    }
}
