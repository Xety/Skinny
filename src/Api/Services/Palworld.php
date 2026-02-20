<?php
namespace Skinny\Api\Services;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Palworld
{
    use ServicesTrait;

    /**
     * Create a Palworld link for a Discord user.
     *
     * @param string $discordId The Discord ID.
     * @param string $playerUid The Palworld Player UID.
     * @param string $playerName The player name in-game.
     *
     * @return null|\stdClass The created link data.
     *
     * @throws \Exception When the link creation fails.
     */
    public function createLink(string $discordId, string $playerUid, string $playerName)
    {
        try {
            return $this->build('POST', 'palworld/links', [
                'discord_id' => $discordId,
                'player_uid' => $playerUid,
                'player_name' => $playerName,
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();

            if ($statusCode === 409) {
                throw new \Exception('This Palworld account is already linked to another Discord user');
            }

            throw new \Exception('Failed to create Palworld link: ' . $e->getMessage());
        } catch (ServerException $e) {
            throw new \Exception('Server error while creating Palworld link: ' . $e->getMessage());
        }
    }

    /**
     * Get a Palworld link by Discord ID.
     *
     * @param string $discordId The Discord ID.
     *
     * @return null|\stdClass The link data or null if not found.
     */
    public function getLinkByDiscord(string $discordId)
    {
        try {
            return $this->build('GET', sprintf('palworld/links/discord/%s', $discordId));
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return null;
            }

            throw new \Exception('Failed to get Palworld link: ' . $e->getMessage());
        }
    }

    /**
     * Get a Palworld link by Player UID (reverse lookup).
     *
     * @param string $playerUid The Palworld Player UID.
     *
     * @return null|\stdClass The link data or null if not found.
     */
    public function getLinkByPlayerUid(string $playerUid)
    {
        try {
            return $this->build('GET', sprintf('palworld/links/player/%s', $playerUid));
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return null;
            }

            throw new \Exception('Failed to get Palworld link: ' . $e->getMessage());
        }
    }

    /**
     * Delete a Palworld link by Discord ID.
     *
     * @param string $discordId The Discord ID.
     *
     * @return bool True if deleted, false if not found.
     *
     * @throws \Exception When the deletion fails.
     */
    public function deleteLink(string $discordId): bool
    {
        try {
            $this->build('DELETE', sprintf('palworld/links/discord/%s', $discordId));
            return true;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return false;
            }

            throw new \Exception('Failed to delete Palworld link: ' . $e->getMessage());
        } catch (ServerException $e) {
            throw new \Exception('Server error while deleting Palworld link: ' . $e->getMessage());
        }
    }
}
