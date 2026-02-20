<?php
declare(strict_types=1);

namespace Skinny\Middleware;

use Skinny\Network\Wrapper;

/**
 * Interface pour les middlewares de commande
 */
interface MiddlewareInterface
{
    /**
     * Exécute le middleware
     *
     * @param Wrapper $wrapper
     * @param array $message
     * @param callable $next
     *
     * @return mixed
     */
    public function handle(Wrapper $wrapper, array $message, callable $next): mixed;
}
