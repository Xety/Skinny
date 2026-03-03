<?php
/**
 * Configure paths required to find general filepath constants.
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'paths.php';

/**
 * Use composer to load the autoloader.
 */
require ROOT . DS . 'vendor' . DS . 'autoload.php';

/**
 * Load environment variables from .env file.
 */
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
if (file_exists(ROOT . DS . '.env')) {
    $dotenv->load();
}

use Skinny\Core\Configure;
use Skinny\Core\Configure\Engine\PhpConfig;
use Skinny\Core\Plugin;

/**
 * Read configuration file and inject configuration into various
 * Skinny classes.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('config');
    Configure::load('commands');
} catch (\Exception $e) {
    die($e->getMessage() . "\n");
}

/**
 * Set server timezone to UTC. You can change it to another timezone of your
 * choice but using UTC makes time calculations / conversions easier.
 */
date_default_timezone_set('Europe/Paris');

/**
 * Set time limit to unlimited or the script will ended itself.
 */
set_time_limit(0);

/**
 * Set the memory unlimited.
 */
ini_set('memory_limit', '200M');

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need.
 *
 * Plugin::loadAll(); // Loads all plugins at once
 * Plugin::load('Basic', ['bootstrap' => true]); //Loads a single plugin named Basic with the bootstrap file.
 *
 */
Plugin::load([
    'Module',
    'Developer',
    //'Member',
    //'Dons',
    'Text',
    //'Vocal',
    'Admin',
    //'Ticket',
    //'Steam',
    //'Moderation',
    'Palworld',
    'Vote'
], ['bootstrap' => true]);
