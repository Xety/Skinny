<?php
declare(strict_types=1);

namespace Skinny\Network;

use Discord\Discord;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Activity;
use Discord\Parts\User\Member;
use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\MessageReaction;
use Discord\Parts\WebSockets\VoiceStateUpdate;
use Skinny\Core\Configure;
use Skinny\Event\Event as BotEvent;
use Skinny\Event\EventDispatcher;
use Skinny\Event\Events;
use Skinny\Message\Message as MessageParser;
use Skinny\Middleware\LoggingMiddleware;
use Skinny\Middleware\MiddlewarePipeline;
use Skinny\Middleware\PermissionMiddleware;
use Skinny\Middleware\RateLimitMiddleware;
use Skinny\Module\ModuleManager;
use Skinny\Network\Wrapper;
use Skinny\Utility\Command;
use Skinny\Utility\User;

class Server
{
    /**
     * The Discord instance.
     *
     * @var \Discord\Discord
     */
    public Discord $Discord;

    /**
     * The Module Manager instance.
     *
     * @var \Skinny\Module\ModuleManager
     */
    public ModuleManager $ModuleManager;

    /**
     * The Event Dispatcher instance.
     *
     * @var \Skinny\Event\EventDispatcher
     */
    public EventDispatcher $EventDispatcher;

    /**
     * The Middleware Pipeline for commands.
     *
     * @var \Skinny\Middleware\MiddlewarePipeline
     */
    public MiddlewarePipeline $MiddlewarePipeline;

    /**
     * Initialize the Bot and and the ModuleManager.
     */
    public function __construct()
    {
        Configure::checkTokenKey();

        // Create the client with Intents (required for v10+)
        $this->Discord = new Discord([
            'token' => Configure::read('Bot.token'),
            'intents' => Intents::getDefaultIntents()
                | Intents::GUILD_MEMBERS
                | Intents::MESSAGE_CONTENT
                | Intents::GUILD_VOICE_STATES
                | Intents::GUILD_MESSAGE_REACTIONS,
            'loadAllMembers' => true,
            'storeMessages' => true,
            'retrieveBans' => true,
        ]);

        // Initialize the ModuleManager with logger
        $modulesPriorities = [];
        if (Configure::check('Modules.priority')) {
            $modulesPriorities = Configure::read('Modules.priority');
        }

        $this->ModuleManager = new ModuleManager($modulesPriorities);

        // Initialize the Event Dispatcher
        $this->EventDispatcher = new EventDispatcher();

        // Initialize the Middleware Pipeline
        $this->MiddlewarePipeline = new MiddlewarePipeline();
        $this->MiddlewarePipeline
            ->pipe(new LoggingMiddleware())
            ->pipe(new PermissionMiddleware())
            ->pipe(new RateLimitMiddleware());
    }

    /**
     * Handle the events.
     *
     * @return void
     */
    public function listen(): void
    {
        $this->Discord->on('init', function (Discord $discord) {
            // Set the logger for ModuleManager
            $this->ModuleManager->setLogger($discord->getLogger());

            // Update the presence using new API
            $activity = new Activity($discord, [
                'name' => Configure::read('Bot.game'),
                'type' => Activity::TYPE_WATCHING
            ]);
            $discord->updatePresence($activity, false);

            $discord->getLogger()->info("Successfully logged into {$discord->user->username}");

            // Dispatch bot ready event
            $this->EventDispatcher->dispatch(Events::BOT_READY, new BotEvent(Events::BOT_READY));
        });

        // Event::MESSAGE_CREATE
        $this->Discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            // Handle webhook messages (for Palworld integration)
            if ($message->webhook_id !== null) {
                $this->handleWebhookMessage($message, $discord);
                return;
            }

            // Check if the author of the message is not a bot.
            if ($message->author === null ||
                $message->author->id == $discord->id ||
                $message->author->bot
            ) {
                return;
            }

            // Parse the message.
            $content = MessageParser::parse($message->content ?? '');

            // Initialise the Wrapper.
            $wrapper = Wrapper::getInstance()->setInstances($this->ModuleManager, $discord, $message);

            // Moderation - Use configured channel IDs instead of hardcoded values
            $moderatedChannels = Configure::read('Discord.channels.moderated') ?? [];

            if (in_array($wrapper->Message->channel->id, $moderatedChannels['support-admin'] ?? [])) {
                $this->ModuleManager->moderationSupportAdmin($wrapper, $content);
            }
            if (in_array($wrapper->Message->channel->id, $moderatedChannels['reglement'] ?? [])) {
                $this->ModuleManager->moderationReglementDiscord($wrapper, $content);
            }

            // Handle the type of the message.
            // Note: The order is very important!
            if ($wrapper->Message->channel->is_private) {
                $this->ModuleManager->onPrivateMessage($wrapper, $content);
            } elseif ($content['commandCode'] === Configure::read('Command.prefix') &&
                        isset(Configure::read('Commands')[$content['command']])) {

                // Process command through middleware pipeline
                $this->MiddlewarePipeline->process(
                    $wrapper,
                    $content,
                    function (Wrapper $wrapper, array $content) {
                        // Check the syntax of the command.
                        $command = Configure::read('Commands')[$content['command']];
                        if (count($content['arguments']) < $command['params']) {
                            $wrapper->Message->reply(Command::syntax($content));
                            return;
                        }

                        $this->ModuleManager->onCommandMessage($wrapper, $content);
                    }
                );
            } else {
                $this->ModuleManager->onChannelMessage($wrapper, $content);
            }
        });

        // Event::GUILD_MEMBER_ADD
        $this->Discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
            $wrapper = Wrapper::getInstance()->setInstances($this->ModuleManager, $discord);

            $this->ModuleManager->onGuildMemberAdd($wrapper, $member);
        });

        // Event::GUILD_MEMBER_REMOVE
        $this->Discord->on(Event::GUILD_MEMBER_REMOVE, function (Member $member, Discord $discord) {
            $wrapper = Wrapper::getInstance()->setInstances($this->ModuleManager, $discord);

            $this->ModuleManager->onGuildMemberRemove($wrapper, $member);
        });

        // Event::MESSAGE_REACTION_ADD
        $this->Discord->on(Event::MESSAGE_REACTION_ADD, function (MessageReaction $reaction, Discord $discord) {
            $wrapper = Wrapper::getInstance()->setInstances($this->ModuleManager, $discord);

            $this->ModuleManager->onMessageReactionAdd($wrapper, $reaction);
        });

        /**
         * Event::VOICE_STATE_UPDATE
         * Vocal click-here feature
         */
        $this->Discord->on(Event::VOICE_STATE_UPDATE, function (VoiceStateUpdate $state, Discord $discord, ?VoiceStateUpdate $oldState) {
            $wrapper = Wrapper::getInstance()->setInstances($this->ModuleManager, $discord);

            $this->ModuleManager->onVoiceStateUpdate($wrapper, $state, $oldState);
        });

        // Error handling
        $this->Discord->on('error', function (\Throwable $error, Discord $discord) {
            $discord->getLogger()->error($error->getMessage(), [
                'exception' => $error,
                'trace' => $error->getTraceAsString()
            ]);

            // Dispatch error event
            $this->EventDispatcher->dispatch(Events::BOT_ERROR, new BotEvent(Events::BOT_ERROR, null, [
                'error' => $error
            ]));
        });
    }

    /**
     * Handle webhook messages (e.g., from game servers like Palworld).
     *
     * @param \Discord\Parts\Channel\Message $message The webhook message.
     * @param \Discord\Discord $discord The Discord instance.
     *
     * @return void
     */
    private function handleWebhookMessage(Message $message, Discord $discord): void
    {
        // Get configured Palworld webhook channels
        $palworldChannels = Configure::read('Discord.channels.palworld') ?? [];

        // Check if the message is from a Palworld channel
        if (!in_array($message->channel_id, $palworldChannels)) {
            return;
        }

        // Initialise the Wrapper
        $wrapper = Wrapper::getInstance()->setInstances($this->ModuleManager, $discord, $message);

        // Forward to ModuleManager for Palworld processing
        $this->ModuleManager->onWebhookMessage($wrapper, $message);
    }

    /**
     * Run the bot.
     *
     * @return void
     */
    public function startup(): void
    {
        $this->listen();

        $this->Discord->run();
    }
}
