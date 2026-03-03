<?php
declare(strict_types=1);

namespace Vote\Module\Modules;

use Discord\Parts\Channel\Message;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;

/**
 * Module Vote - Détecte les votes top-serveurs.net et les enregistre via l'API.
 *
 * Ce module écoute les messages dans les channels de vote configurés
 * pour détecter les messages de type "{user_id}Username vient de voter pour le serveur !".
 *
 * Flux de vote :
 * 1. Un utilisateur vote sur top-serveurs.net
 * 2. Le bot top-serveurs poste un message dans le channel Discord configuré
 * 3. Ce module détecte le message, extrait l'user_id via regex
 * 4. Il appelle l'API Laravel pour enregistrer le vote
 * 5. Réaction ✅ si succès, ❓ si l'utilisateur n'est pas identifié
 */
class Vote implements ModuleInterface
{
    /**
     * Nom du module.
     */
    protected string $name = 'Vote';

    /**
     * Description du module.
     */
    protected string $description = 'Gestion des votes top-serveurs.net';

    /**
     * Version du module.
     */
    protected string $version = '1.0.0';

    /**
     * Mapping channel_id → game_id chargé depuis l'API.
     *
     * @var array<string, string>
     */
    private static array $channelGameMap = [];

    /**
     * Indique si la config a déjà été chargée.
     *
     * @var bool
     */
    private static bool $configLoaded = false;

    /**
     * Patterns regex pour extraire l'user_id du message de vote.
     *
     * Formats supportés :
     * - "{user_id}Username vient de voter pour le serveur !"  → ID en préfixe
     * - "Username{user_id} vient de voter pour le serveur !"  → ID en suffixe
     * - "{user_id} vient de voter pour le serveur !"          → ID seul
     */
    private const VOTE_PATTERNS = [
        // ID en préfixe : "42ZoRo vient de voter" ou "42 vient de voter"
        '/^(\d+)\S*\s+vient de voter/i',
        // ID en suffixe : "ZoRo42 vient de voter"
        '/^\S+?(\d+)\s+vient de voter/i',
    ];

    /**
     * {@inheritDoc}
     *
     * Charge la configuration des channels de vote depuis l'API
     * et les enregistre dans Configure pour que Server.php puisse
     * router les messages bot vers ce module.
     */
    public function __construct()
    {
        $this->loadVoteConfig();
    }

    /**
     * {@inheritDoc}
     *
     * Traite les messages dans les channels.
     * Vérifie si le message provient d'un channel de vote et s'il correspond
     * à un pattern de vote top-serveurs.net.
     */
    public function onChannelMessage(Wrapper $wrapper, array $content): void
    {
        // Ne traiter que si le message est dans un channel de vote
        if ($wrapper->Message === null) {
            return;
        }

        $channelId = $wrapper->Message->channel_id;

        if (!isset(self::$channelGameMap[$channelId])) {
            return;
        }

        $this->processVoteMessage($wrapper, $wrapper->Message);
    }

    /**
     * Traite un message potentiel de vote.
     *
     * @param \Skinny\Network\Wrapper $wrapper L'instance Wrapper.
     * @param \Discord\Parts\Channel\Message $message Le message Discord.
     *
     * @return void
     */
    private function processVoteMessage(Wrapper $wrapper, Message $message): void
    {
        $content = $message->content ?? '';
        $channelId = $message->channel_id;

        if (empty($content)) {
            return;
        }

        // Vérifier que c'est bien un message de vote (contient "vient de voter")
        if (stripos($content, 'vient de voter') === false) {
            return;
        }

        // Essayer d'extraire l'user_id du message
        $userId = $this->extractUserId($content);

        if ($userId === null) {
            // Impossible d'extraire l'user_id → réagir avec ❓ pour indiquer une incertitude
            $message->react('❓');
            debug("Message de vote détecté mais user_id non trouvé: {$content}");

            return;
        }

        // Enregistrer le vote via l'API
        try {
            $response = $wrapper->API->vote()->register($userId, $channelId);

            // Vérifier si l'API a retourné une erreur (validation, user not found, etc.)
            $responseData = is_array($response) ? $response : json_decode(json_encode($response), true);

            if (isset($responseData['errors']) || isset($responseData['message'])) {
                $errorMsg = $responseData['message'] ?? 'Erreur inconnue';
                $message->react('❓');
                debug("Vote rejeté par l'API pour user_id={$userId}: {$errorMsg}");

                return;
            }

            // Vote enregistré avec succès
            $message->react('✅');
            debug("Vote enregistré pour user_id={$userId} channel={$channelId}");
        } catch (\Exception $e) {
            // Utilisateur non trouvé ou erreur
            $message->react('❓');
            debug("Erreur lors de l'enregistrement du vote: {$e->getMessage()}");
        }
    }

    /**
     * Extrait l'user_id du message de vote.
     *
     * Teste les différents patterns supportés dans l'ordre.
     *
     * @param string $content Le contenu du message.
     *
     * @return int|null L'user_id extrait ou null si non trouvé.
     */
    private function extractUserId(string $content): ?int
    {
        foreach (self::VOTE_PATTERNS as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $id = (int) $matches[1];

                // L'ID doit être positif et raisonnable
                if ($id > 0 && $id < 2147483647) {
                    return $id;
                }
            }
        }

        return null;
    }

    /**
     * Charge la configuration des channels de vote depuis l'API.
     *
     * Stocke le mapping channel_id → game_id en mémoire
     * et enregistre les channel IDs dans Configure pour que
     * Server.php puisse router les messages bot vers ce module.
     *
     * @return void
     */
    private function loadVoteConfig(): void
    {
        if (self::$configLoaded) {
            return;
        }

        try {
            $config = (new \Skinny\Api\Services\Vote())->config();

            if ($config === null) {
                debug('Aucune configuration de vote trouvée');
                self::$configLoaded = true;
                return;
            }

            // La réponse est un objet avec les channels mappés aux jeux
            // Format attendu: { "channels": { "channel_id": "game_id", ... } }
            if (is_object($config) && isset($config->channels)) {
                foreach ($config->channels as $channelId => $game) {
                    self::$channelGameMap[(string) $channelId] = (string) $game->game_id;
                }
            } elseif (is_array($config)) {
                foreach ($config as $item) {
                    if (isset($item->channel_id, $item->game_id)) {
                        self::$channelGameMap[(string) $item->channel_id] = (string) $item->game_id;
                    }
                }
            }

            // Enregistrer les channel IDs dans Configure pour Server.php
            $channelIds = array_keys(self::$channelGameMap);
            Configure::write('Discord.channels.vote', $channelIds);

            self::$configLoaded = true;


            debug('Configuration de vote chargée: ' . count(self::$channelGameMap));
        } catch (\Exception $e) {
            debug('Impossible de charger la configuration de vote: ' . $e->getMessage());
            self::$configLoaded = true; // Ne pas réessayer en boucle
        }
    }

    /**
     * Retourne le mapping channel → game (pour debug/tests).
     *
     * @return array<string, string>
     */
    public static function getChannelGameMap(): array
    {
        return self::$channelGameMap;
    }

    /**
     * Réinitialise la configuration (pour les tests).
     *
     * @return void
     */
    public static function resetConfig(): void
    {
        self::$channelGameMap = [];
        self::$configLoaded = false;
    }
}
