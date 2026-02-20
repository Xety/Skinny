<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Psr7\Response;

class Server
{
    use ServicesTrait;

    /**
     * Get a server by his id.
     *
     * @param int $id The id of the server.
     *
     * @return null|\stdClass
     */
    public function get(int $id)
    {
        return $this->build('GET', sprintf('server/%d', $id));
    }

    /**
     * Get all servers.
     *
     * @return null|\stdClass
     */
    public function getAll()
    {
        return $this->build('GET', 'servers');
    }
}
