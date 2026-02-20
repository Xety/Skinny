<?php
declare(strict_types=1);

namespace Skinny\Module;

use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\Parts\WebSockets\MessageReaction;
use Discord\Parts\WebSockets\VoiceStateUpdate;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Skinny\Network\Wrapper;

/**
 * Classe abstraite de base pour tous les modules
 *
 * Fournit des implémentations par défaut et des méthodes utilitaires
 */
abstract class AbstractModule implements ModuleInterface
{
    /**
     * Logger PSR-3
     */
    protected LoggerInterface $logger;

    /**
     * Nom du module
     */
    protected string $name;

    /**
     * Description du module
     */
    protected string $description = '';

    /**
     * Version du module
     */
    protected string $version = '1.0.0';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = new NullLogger();
        $this->name = static::class;
    }

    /**
     * Définit le logger
     *
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Retourne le nom du module
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retourne la description du module
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Retourne la version du module
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Appelé lors du chargement du module
     *
     * @return void
     */
    public function onLoad(): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé lors du déchargement du module
     *
     * @return void
     */
    public function onUnload(): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé pour les messages de commande
     *
     * @param Wrapper $wrapper
     * @param array $message
     *
     * @return void
     */
    public function onCommandMessage(Wrapper $wrapper, array $message): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé pour les messages de channel
     *
     * @param Wrapper $wrapper
     * @param array $message
     *
     * @return void
     */
    public function onChannelMessage(Wrapper $wrapper, array $message): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé pour les messages privés
     *
     * @param Wrapper $wrapper
     * @param array $message
     *
     * @return void
     */
    public function onPrivateMessage(Wrapper $wrapper, array $message): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé quand un membre rejoint le serveur
     *
     * @param Wrapper $wrapper
     * @param Member $member
     *
     * @return void
     */
    public function onGuildMemberAdd(Wrapper $wrapper, Member $member): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé quand un membre quitte le serveur
     *
     * @param Wrapper $wrapper
     * @param Member $member
     *
     * @return void
     */
    public function onGuildMemberRemove(Wrapper $wrapper, Member $member): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé quand une réaction est ajoutée
     *
     * @param Wrapper $wrapper
     * @param MessageReaction $reaction
     *
     * @return void
     */
    public function onMessageReactionAdd(Wrapper $wrapper, MessageReaction $reaction): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Appelé lors d'un changement d'état vocal
     *
     * @param Wrapper $wrapper
     * @param VoiceStateUpdate $state
     * @param VoiceStateUpdate|null $oldState
     *
     * @return void
     */
    public function onVoiceStateUpdate(Wrapper $wrapper, VoiceStateUpdate $state, ?VoiceStateUpdate $oldState): void
    {
        // Override dans les sous-classes si nécessaire
    }

    /**
     * Log un message de debug
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    protected function debug(string $message, array $context = []): void
    {
        $this->logger->debug("[{$this->name}] {$message}", $context);
    }

    /**
     * Log un message d'info
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    protected function info(string $message, array $context = []): void
    {
        $this->logger->info("[{$this->name}] {$message}", $context);
    }

    /**
     * Log un message d'erreur
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    protected function error(string $message, array $context = []): void
    {
        $this->logger->error("[{$this->name}] {$message}", $context);
    }
}
