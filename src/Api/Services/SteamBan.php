<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Psr7\Response;

class SteamBan
{
    use ServicesTrait;

    /**
     * Get a SteamBan by his steam_id.
     *
     * @param int $id The id of the user.
     *
     * @return null|\stdClass
     */
    public function get(int $id)
    {
        return $this->build('GET', sprintf('steamban/%d', $id));
    }

    /**
     * Create a SteamBan.
     *
     * @param array $data All data used to create the ban.
     *
     * @return null|\stdClass
     */
    public function create(array $data)
    {
        return $this->build('POST', 'steamban/create', $data);
    }

    /**
     * Get a SteamBan by his steam_id.
     *
     * @param int $id The id of the user.
     *
     * @return null|\stdClass
     */
    public function checkBan(int $id)
    {
        return $this->build('GET', sprintf('steamban/checkban/%d', $id));
    }
}
