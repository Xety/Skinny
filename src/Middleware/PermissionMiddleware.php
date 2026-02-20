<?php
declare(strict_types=1);

namespace Skinny\Middleware;

use Skinny\Core\Configure;
use Skinny\Network\Wrapper;
use Skinny\Utility\User;

/**
 * Middleware de vérification des permissions
 */
class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(Wrapper $wrapper, array $message, callable $next): mixed
    {
        $command = $message['command'] ?? null;

        if ($command === null) {
            return $next($wrapper, $message);
        }

        $commandConfig = Configure::read("Commands.{$command}");

        if ($commandConfig === null) {
            return $next($wrapper, $message);
        }

        // Vérifier les permissions admin
        if (isset($commandConfig['admin']) && $commandConfig['admin'] === true) {
            if (!User::hasPermission($wrapper, Configure::read('Discord.admins')) &&
                !User::hasPermission($wrapper, Configure::read('Discord.developers'))) {
                $wrapper->Message->reply(
                    ':octagonal_sign: Vous n\'êtes pas autorisé à utiliser cette commande. :octagonal_sign:'
                );
                return null;
            }
        }

        // Vérifier les permissions développeur
        if (isset($commandConfig['developer']) && $commandConfig['developer'] === true) {
            if (!User::hasPermission($wrapper, Configure::read('Discord.developers'))) {
                $wrapper->Message->reply(
                    ':octagonal_sign: Vous n\'êtes pas développeur du bot. :octagonal_sign:'
                );
                return null;
            }
        }

        return $next($wrapper, $message);
    }
}
