<?php
declare(strict_types=1);

namespace Palworld\Module\Modules;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;

/**
 * Module Palworld - Gère la liaison des comptes Discord avec les PlayerUID Palworld.
 *
 * Ce module permet aux joueurs de lier leur compte Discord avec leur identifiant
 * Palworld en utilisant un système de codes temporaires.
 *
 * Flux de liaison :
 * 1. L'utilisateur fait !pallink sur Discord -> reçoit un code unique
 * 2. L'utilisateur fait !link [code] dans le chat in-game Palworld
 * 3. Le webhook Palworld envoie le message au channel Discord
 * 4. Le bot détecte le message, extrait le PlayerUID et lie les comptes
 */
class Palworld implements ModuleInterface
{
    /**
     * Stockage des codes de liaison en attente.
     * Format: ['code' => ['discord_id' => string, 'created_at' => int, 'expires_at' => int]]
     *
     * @var array
     */
    private static array $pendingLinks = [];

    /**
     * Durée de validité d'un code en secondes (20 minutes).
     */
    private const CODE_EXPIRY = 1200;

    /**
     * {@inheritDoc}
     */
    public function onChannelMessage(Wrapper $wrapper, array $content): void
    {
        // Get configured Palworld webhook channels
        $palworldChannels = Configure::read('Discord.channels.palworld') ?? [];

        // Check if the message is from a Palworld channel
        if (
            !in_array($wrapper->Message->channel_id, $palworldChannels) ||
            $wrapper->Message->webhook_id === null
        ){
            return;
        }

        $this->onWebhookMessage($wrapper, $wrapper->Message);
    }

    /**
     * {@inheritDoc}
     *
     * Route les commandes vers les bonnes méthodes.
     */
    public function onCommandMessage(Wrapper $wrapper, array $message): void
    {
        switch ($message['command']) {
            case 'pallink':
                $this->onCommandPallink($wrapper, $message);
                break;
            case 'palstatus':
                $this->onCommandPalstatus($wrapper, $message);
                break;
            case 'palunlink':
                $this->onCommandPalunlink($wrapper, $message);
                break;
        }
    }

    /**
     * Analyse les messages webhook de Palworld pour détecter les commandes !link.
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param \Discord\Parts\Channel\Message $message The message from webhook.
     *
     * @return void
     */
    public function onWebhookMessage(Wrapper $wrapper, Message $message): void
    {
        // Vérifier si c'est un message avec embed
        if ($message->embeds->count() === 0) {
            return;
        }

        foreach ($message->embeds as $embed) {
            $this->processEmbed($wrapper, $embed, $message);
        }
    }

    /**
     * Traite un embed pour extraire les informations de liaison.
     *
     * @param \Skinny\Network\Wrapper $wrapper
     * @param \Discord\Parts\Embed\Embed $embed
     * @param \Discord\Parts\Channel\Message $message
     *
     * @return void
     */
    private function processEmbed(Wrapper $wrapper, Embed $embed, Message $message): void
    {
        // Le titre de l'embed doit être "Chat Log"
        if ($embed->title !== 'Chat Log') {
            return;
        }

        $description = $embed->description ?? '';

        // Pattern pour extraire: [Global] PlayerName [PlayerUID]: !link CODE
        // Exemple: [Global] ZoRo [43BA6413-00000000-00000000-00000000]: !link 123
        // ou: [Global] Player [FFFFFFFFA249B111-00000000-00000000-00000000]: !link CODE
        $pattern = '/\[(?:Global|Local|Guild)\]\s+(.+?)\s+\[([A-F0-9]{8,16}-[A-F0-9]{8}-[A-F0-9]{8}-[A-F0-9]{8})\]:\s*!link\s+(\w+)/i';

        if (preg_match($pattern, $description, $matches)) {
            $playerName = trim($matches[1]);
            $playerUID = $matches[2];
            $linkCode = $matches[3];

            $this->processLinkRequest($wrapper, $message, $playerName, $playerUID, $linkCode);
        }
    }

    /**
     * Traite une demande de liaison.
     *
     * @param \Skinny\Network\Wrapper $wrapper
     * @param \Discord\Parts\Channel\Message $message
     * @param string $playerName
     * @param string $playerUID
     * @param string $linkCode
     *
     * @return void
     */
    private function processLinkRequest(
        Wrapper $wrapper,
        Message $message,
        string $playerName,
        string $playerUID,
        string $linkCode
    ): void {
        // Nettoyer les codes expirés
        $this->cleanExpiredCodes();

        // Vérifier si le code existe
        if (!isset(self::$pendingLinks[$linkCode])) {
            $this->sendErrorEmbed(
                $wrapper,
                $message->channel,
                'Code invalide',
                "Le code `{$linkCode}` n'existe pas ou a expiré.\nUtilisez `!pallink` sur Discord pour générer un nouveau code."
            );
            return;
        }

        $pendingLink = self::$pendingLinks[$linkCode];

        // Vérifier si le code a expiré
        if (time() > $pendingLink['expires_at']) {
            unset(self::$pendingLinks[$linkCode]);
            $this->sendErrorEmbed(
                $wrapper,
                $message->channel,
                'Code expiré',
                "Le code `{$linkCode}` a expiré.\nUtilisez `!pallink` sur Discord pour générer un nouveau code."
            );
            return;
        }

        $discordId = $pendingLink['discord_id'];

        // Normaliser le PlayerUID : si le premier segment fait 16 chars (ex: FFFFFFFFA249B111),
        // on ne garde que les 8 derniers (ex: A249B111).
        $playerUID = preg_replace('/^[A-F0-9]{8}([A-F0-9]{8}-)/i', '$1', $playerUID);

        // Effectuer la liaison via l'API
        try {
            $wrapper->API->palworld()->createLink($discordId, $playerUID, $playerName);

            // Supprimer le code utilisé
            unset(self::$pendingLinks[$linkCode]);

            // Ajouter une réaction au message pour confirmer
            $message->react('✅');

            // Envoyer un MP à l'utilisateur
            $wrapper->Discord->users->fetch($discordId)->then(function ($user) use ($wrapper, $playerName, $playerUID) {
                $embed = new Embed($wrapper->Discord);
                $embed->setTitle('Liaison réussie ! ✅')
                    ->setDescription("Ton compte Palworld a été lié avec succès !\n\n**Joueur:** {$playerName}\n**PlayerUID:** `{$playerUID}`")
                    ->setColor(0x2ECC71)
                    ->setFooter('Palworld Division')
                    ->setTimestamp();

                $user->sendMessage(MessageBuilder::new()->addEmbed($embed));
            });
        } catch (\Exception $e) {
            // En cas d'erreur API, ajouter une réaction d'erreur
            $message->react('❌');

            // Envoyer un MP à l'utilisateur avec l'erreur
            $wrapper->Discord->users->fetch($discordId)->then(function ($user) use ($wrapper, $e) {
                $embed = new Embed($wrapper->Discord);
                $embed->setTitle('❌ Erreur de liaison')
                    ->setDescription("Impossible de créer la liaison : " . $e->getMessage())
                    ->setColor(0xE74C3C)
                    ->setFooter('Palworld Division')
                    ->setTimestamp();

                $user->sendMessage(MessageBuilder::new()->addEmbed($embed));
            });
        }
    }

    /**
     * Commande !pallink - Génère un code de liaison.
     *
     * @param \Skinny\Network\Wrapper $wrapper
     * @param array $content
     *
     * @return void
     */
    public function onCommandPallink(Wrapper $wrapper, array $content): void
    {
        $discordId = $wrapper->Message->author->id;

        // Vérifier si l'utilisateur est déjà lié via l'API
        try {
            $existingLink = $wrapper->API->palworld()->getLinkByDiscord($discordId);

        } catch (\Exception $e) {
            // Si c'est une vraie erreur (pas un 404 "not found"), afficher un message
            if (!str_contains($e->getMessage(), 'No Palworld link found')) {
                $embed = new Embed($wrapper->Discord);
                $embed->setTitle('❌ Erreur')
                    ->setDescription("Impossible de vérifier le statut de liaison. Réessaye plus tard.")
                    ->setColor(0xE74C3C)
                    ->setTimestamp();

                $wrapper->Message->reply(MessageBuilder::new()->addEmbed($embed));
                return;
            }
            // Sinon, l'utilisateur n'est pas lié, on continue le flow normal
        }

        debug($existingLink);

        if (isset($existingLink->error) && $existingLink->error !== 'Not Found') {
            $embed = new Embed($wrapper->Discord);
            $embed->setTitle('❌ Erreur')
                ->setDescription($existingLink->message ?? "Une erreur inconnue est survenue. Réessaye plus tard.")
                ->setColor(0xE74C3C)
                ->setTimestamp();

            $wrapper->Message->reply(MessageBuilder::new()->addEmbed($embed));
            return;
        }

        // Nettoyer les anciens codes de cet utilisateur
        foreach (self::$pendingLinks as $code => $data) {
            if ($data['discord_id'] === $discordId) {
                unset(self::$pendingLinks[$code]);
            }
        }

        // Générer un nouveau code unique
        $code = $this->generateUniqueCode();

        // Stocker le code
        self::$pendingLinks[$code] = [
            'discord_id' => $discordId,
            'created_at' => time(),
            'expires_at' => time() + self::CODE_EXPIRY
        ];

        $expiresIn = (int) (self::CODE_EXPIRY / 60);

        $embed = new Embed($wrapper->Discord);
        $embed->setTitle('🔗 Code de liaison Palworld')
            ->setDescription("Voici ton code de liaison unique :\n\n```\n{$code}\n```")
            ->addFieldValues('📝 Instructions', "1. Connecte-toi à un serveur Palworld Division\n2. Dans le chat du jeu, tape :\n```\n!link {$code}\n```\n3. Ton compte sera automatiquement lié !")
            ->addFieldValues('⏰ Expiration', "Ce code expire dans **{$expiresIn} minutes**")
            ->setColor(0x3498DB)
            ->setFooter('Palworld Division')
            ->setTimestamp();

        $originalMessage = $wrapper->Message;

        // Envoyer en DM si possible, sinon dans le channel
        $wrapper->Message->author
            ->sendMessage(MessageBuilder::new()
            ->addEmbed($embed))
            ->then(function () use ($originalMessage) {
                $originalMessage->react('✅');
            })
            ->otherwise(function () use ($wrapper, $embed) {
                $wrapper->Message->reply(MessageBuilder::new()->addEmbed($embed));
            });
    }

    /**
     * Commande !palstatus - Vérifie le statut de liaison.
     *
     * @param \Skinny\Network\Wrapper $wrapper
     * @param array $content
     *
     * @return void
     */
    public function onCommandPalstatus(Wrapper $wrapper, array $content): void
    {
        $discordId = $wrapper->Message->author->id;

        $embed = new Embed($wrapper->Discord);

        try {
            $linked = $wrapper->API->palworld()->getLinkByDiscord($discordId);

            if (isset($linked->error) && $linked->error === 'Not Found') {
                $embed->setTitle('❌ Non lié')
                    ->setDescription("Ton compte Discord n'est pas lié à Palworld.\n\nUtilise `!pallink` pour obtenir un code de liaison.")
                    ->setColor(0xE74C3C)
                    ->setTimestamp();
            } else {
                $linkedAt = date('d/m/Y à H:i', strtotime($linked->linked_at));

                $embed->setTitle('✅ Compte lié')
                    ->setDescription("Ton compte Discord est lié à Palworld !")
                    ->addFieldValues('👤 Joueur', $linked->player_name, true)
                    ->addFieldValues('🆔 PlayerUID', "`{$linked->player_uid}`", true)
                    ->addFieldValues('📅 Lié le', $linkedAt)
                    ->setColor(0x2ECC71)
                    ->setTimestamp();
            }
        } catch (\Exception $e) {
            $embed->setTitle('❌ Erreur')
                ->setDescription("Impossible de récupérer les informations. Réessaye plus tard.")
                ->setColor(0xE74C3C)
                ->setTimestamp();
        }

        $wrapper->Message->reply(MessageBuilder::new()->addEmbed($embed));
    }

    /**
     * Commande !palunlink - Supprime la liaison.
     *
     * @param \Skinny\Network\Wrapper $wrapper
     * @param array $content
     *
     * @return void
     */
    public function onCommandPalunlink(Wrapper $wrapper, array $content): void
    {
        $discordId = $wrapper->Message->author->id;

        $embed = new Embed($wrapper->Discord);

        try {
            // Récupérer les infos avant suppression
            $linked = $wrapper->API->palworld()->getLinkByDiscord($discordId);

            if (isset($linked->error) && $linked->error === 'Not Found') {
                $embed->setTitle('❌ Non lié')
                    ->setDescription("Ton compte n'est pas lié à Palworld.")
                    ->setColor(0xE74C3C)
                    ->setTimestamp();
            } else {
                    // Supprimer la liaison
                $wrapper->API->palworld()->deleteLink($discordId);

                $embed->setTitle('🔓 Liaison supprimée')
                    ->setDescription("Ton compte Discord n'est plus lié à Palworld.")
                    ->addFieldValues('👤 Ancien joueur', $linked->player_name)
                    ->addFieldValues('🆔 Ancien PlayerUID', "`{$linked->player_uid}`")
                    ->setColor(0xF39C12)
                    ->setTimestamp();
            }
        } catch (\Exception $e) {
            $embed->setTitle('❌ Erreur')
                ->setDescription("Impossible de supprimer la liaison. Réessaye plus tard.")
                ->setColor(0xE74C3C)
                ->setTimestamp();
        }

        $wrapper->Message->reply(MessageBuilder::new()->addEmbed($embed));
    }

    /**
     * Génère un code unique de 6 caractères.
     *
     * @return string
     */
    private function generateUniqueCode(): string
    {
        do {
            // Générer un code alphanumérique de 6 caractères (facile à taper in-game)
            $code = strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));
        } while (isset(self::$pendingLinks[$code]));

        return $code;
    }

    /**
     * Nettoie les codes expirés.
     *
     * @return void
     */
    private function cleanExpiredCodes(): void
    {
        $now = time();
        foreach (self::$pendingLinks as $code => $data) {
            if ($now > $data['expires_at']) {
                unset(self::$pendingLinks[$code]);
            }
        }
    }

    /**
     * Envoie un embed d'erreur.
     *
     * @param \Skinny\Network\Wrapper $wrapper
     * @param mixed $channel
     * @param string $title
     * @param string $description
     *
     * @return void
     */
    private function sendErrorEmbed(Wrapper $wrapper, $channel, string $title, string $description): void
    {
        $embed = new Embed($wrapper->Discord);
        $embed->setTitle("❌ {$title}")
            ->setDescription($description)
            ->setColor(0xE74C3C)
            ->setTimestamp();

        $channel->sendMessage(MessageBuilder::new()->addEmbed($embed));
    }
}
