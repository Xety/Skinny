<?php
declare(strict_types=1);

namespace Skinny\Middleware;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Skinny\Network\Wrapper;

/**
 * Middleware de logging des commandes
 */
class LoggingMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Wrapper $wrapper, array $message, callable $next): mixed
    {
        $startTime = microtime(true);

        $this->logger->info('Command received', [
            'command' => $message['command'] ?? 'unknown',
            'user' => $wrapper->Message?->author?->username ?? 'unknown',
            'channel' => $wrapper->Message?->channel?->name ?? 'unknown',
        ]);

        $result = $next($wrapper, $message);

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $this->logger->debug('Command executed', [
            'command' => $message['command'] ?? 'unknown',
            'execution_time_ms' => $executionTime,
        ]);

        return $result;
    }
}
