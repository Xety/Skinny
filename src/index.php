<?php
require dirname(__DIR__) . '/config/bootstrap.php';

use Skinny\Network\Server;

$server = (new Server())->startup();
