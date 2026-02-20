<?php
declare(strict_types=1);

namespace Skinny\Middleware;

use Skinny\Network\Wrapper;

/**
 * Pipeline de middlewares
 */
class MiddlewarePipeline
{
    /**
     * Les middlewares enregistrés
     *
     * @var MiddlewareInterface[]
     */
    private array $middlewares = [];

    /**
     * Ajoute un middleware au pipeline
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Exécute le pipeline
     *
     * @param Wrapper $wrapper
     * @param array $message
     * @param callable $destination
     *
     * @return mixed
     */
    public function process(Wrapper $wrapper, array $message, callable $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            fn (callable $next, MiddlewareInterface $middleware) =>
                fn (Wrapper $wrapper, array $message) => $middleware->handle($wrapper, $message, $next),
            $destination
        );

        return $pipeline($wrapper, $message);
    }
}
