<?php
declare(strict_types=1);

namespace Skinny\Event;

/**
 * Constantes pour les noms d'événements du bot
 */
final class Events
{
    // Message events
    public const MESSAGE_RECEIVED = 'message.received';
    public const MESSAGE_COMMAND = 'message.command';
    public const MESSAGE_CHANNEL = 'message.channel';
    public const MESSAGE_PRIVATE = 'message.private';

    // Guild member events
    public const GUILD_MEMBER_ADD = 'guild.member.add';
    public const GUILD_MEMBER_REMOVE = 'guild.member.remove';
    public const GUILD_MEMBER_UPDATE = 'guild.member.update';

    // Reaction events
    public const REACTION_ADD = 'reaction.add';
    public const REACTION_REMOVE = 'reaction.remove';

    // Voice events
    public const VOICE_STATE_UPDATE = 'voice.state.update';

    // Moderation events
    public const MODERATION_CHECK = 'moderation.check';

    // Bot lifecycle events
    public const BOT_READY = 'bot.ready';
    public const BOT_ERROR = 'bot.error';

    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }
}
