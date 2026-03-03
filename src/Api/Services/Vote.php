<?php
declare(strict_types=1);

namespace Skinny\Api\Services;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

/**
 * Service API pour le système de votes top-serveurs.net.
 *
 * Permet de :
 * - Récupérer la configuration des channels de vote par jeu
 * - Enregistrer un vote pour un utilisateur
 */
class Vote
{
    use ServicesTrait;

    /**
     * Récupère la configuration des channels de vote.
     *
     * Retourne un mapping channel_id → game pour savoir
     * quels channels écouter et à quel jeu ils correspondent.
     *
     * @return \stdClass|array|null La configuration des votes.
     *
     * @throws \Exception En cas d'erreur API.
     */
    public function config()
    {
        try {
            return $this->build('GET', 'votes/config');
        } catch (ClientException $e) {
            throw new \Exception('Failed to fetch vote config: ' . $e->getMessage());
        } catch (ServerException $e) {
            throw new \Exception('Server error while fetching vote config: ' . $e->getMessage());
        }
    }

    /**
     * Enregistre un vote pour un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur sur le site.
     * @param string $channelId L'ID du channel Discord où le vote a été détecté.
     *
     * @return \stdClass|array|null La réponse de l'API.
     *
     * @throws \Exception En cas d'erreur API.
     */
    public function register(int $userId, string $channelId)
    {
        try {
            return $this->build('POST', 'votes/register', [
                'user_id' => $userId,
                'channel_id' => $channelId,
            ]);
        } catch (ClientException $e) {
            throw new \Exception('Failed to register vote: ' . $e->getMessage());
        } catch (ServerException $e) {
            throw new \Exception('Server error while registering vote: ' . $e->getMessage());
        }
    }
}
