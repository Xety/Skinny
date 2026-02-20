<?php
declare(strict_types=1);

namespace Skinny\Middleware;

use Skinny\Core\Configure;
use Skinny\Network\Wrapper;
use Skinny\Service\RateLimiter;

/**
 * Middleware de rate limiting (cooldown) des commandes
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    private RateLimiter $rateLimiter;

    public function __construct(?RateLimiter $rateLimiter = null)
    {
        $this->rateLimiter = $rateLimiter ?? new RateLimiter();
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Wrapper $wrapper, array $message, callable $next): mixed
    {
        $command = $message['command'] ?? null;
        $userId = $wrapper->Message?->author?->id;

        if ($command === null || $userId === null) {
            return $next($wrapper, $message);
        }

        $commandConfig = Configure::read("Commands.{$command}");
        $cooldown = $commandConfig['cooldown'] ?? Configure::read('Command.defaultCooldown') ?? 0;

        if ($cooldown > 0) {
            $key = "{$userId}:{$command}";

            if (!$this->rateLimiter->attempt($key, $cooldown)) {
                $remaining = $this->rateLimiter->getRemainingTime($key);
                $wrapper->Message->reply(
                    ":hourglass: Veuillez attendre **{$remaining}** seconde(s) avant de réutiliser cette commande."
                );
                return null;
            }
        }

        return $next($wrapper, $message);
    }
}
