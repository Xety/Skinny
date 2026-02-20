<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Psr7\Response;

class User
{
    use ServicesTrait;

    /**
     * Get a user by his id.
     *
     * @param int $id The id of the user.
     *
     * @return null|\stdClass
     */
    public function get(int $id)
    {
        return $this->build('GET', sprintf('user/%d', $id));
    }

    /**
     * Get a user by his discord id.
     *
     * @param int $id The discord id of the user.
     *
     * @return null|\stdClass
     */
    public function getByDiscord(int $id)
    {
        return $this->build('GET', sprintf('user/discord/%d', $id));
    }

    /**
     * Get a user by his steam id.
     *
     * @param int $id The steam id of the user.
     *
     * @return null|\stdClass
     */
    public function getBySteam(int $id)
    {
        return $this->build('GET', sprintf('user/steam/%d', $id));
    }

    /**
     * Update a user by his discord id.
     *
     * @param int $id The discord id of the user.
     * @param array $data All data to update.
     *
     * @return null|\stdClass
     */
    public function updateByDiscord(int $id, array $data)
    {
        return $this->build('PUT', sprintf('user/discord/%d', $id), $data);
    }
}
