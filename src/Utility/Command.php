<?php
namespace Skinny\Utility;

use Skinny\Core\Configure;

class Command
{
    /**
     * Return the syntax of a command formated.
     *
     * @param array $message The message array.
     *
     * @return string The syntax formated.
     */
    public static function syntax($message)
    {
        return 'Vous n\'avez pas précisé assez de paramètres. Syntaxe : `' . Configure::read('Command.prefix') .
            Configure::read('Commands')[$message['command']]['syntax'] . '`';
    }

    /**
     * Return the syntax of a unknow command formated.
     *
     * @param array $message The message array.
     *
     * @return string The syntax formated.
     */
    public static function unknown($message)
    {
        return 'Commande inconnue. Syntaxe : `' . Configure::read('Commands')[$message['command']]['syntax'] . '`';
    }
}
