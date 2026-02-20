<?php
namespace Ticket\Module\Modules;

use Cake\Chronos\Chronos;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Embed\Embed;
use Discord\Parts\WebSockets\MessageReaction;
use DateTime;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;

class Ticket implements ModuleInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param array $message The message array.
     *
     * @return void
     */
    public function onCommandMessage(Wrapper $wrapper, array $message): void
    {
        if (count($message['arguments']) < Configure::read('Commands.' . $message['command'] . '.params')) {
            $wrapper->Message->reply(Command::syntax($message));

            return;
        }

        if ($message['command'] !== 'ticket') {
            return;
        }

        // Handle the command.
        switch ($message['arguments'][0]) {
            case 'open':
                if (!in_array($wrapper->Message->channel_id, Configure::read('Discord.ticket.channels'))) {
                    $wrapper->Message->reply("Cette commande ne peut pas être utilisé dans ce channel.");
                    return;
                }
                //  Get the author id.
                $userId = $wrapper->Message->author->id;

                // Select the user from the database.
                $ticket = $wrapper->API->ticket()->get($userId);

                if (is_null($ticket)) {
                    $date = Chronos::now();

                    // Create a new entry
                    $values = [
                        'discord_id' => $userId,
                        'ticket_count' => 0,
                        'ticket_opened' => 0,
                        'ticket_message_id' => null,
                        'last_ticket_date' => (string)$date->modify('-2 hours')
                    ];

                    // Insert the new Ticket in database.
                    $ticket = $wrapper->API->ticket()->create($values);
                }
                $date = Chronos::now();

                // Check the rate limit for this user.
                if ($ticket->ticket_opened >= Configure::read('Discord.ticket.ticket_limit')) {
                    // Delete the original message from the author.
                    $wrapper->Discord->getLoop()->addTimer(5, function () use ($wrapper) {
                        return $wrapper->Message->delete();
                    });

                    $wrapper->Message
                        ->reply("Vous avez déjà **{$ticket->ticket_opened}** ticket(s) d'ouvert,".
                        " vous ne pouvez pas en réouvrir un nouveau.")
                        ->then(function ($message) use ($wrapper) {
                            $wrapper->Discord->getLoop()->addTimer(8, function () use ($message) {
                                return $message->delete();
                            });
                        });

                    return;
                }

                // Check the last opened ticket for avoid spamming.
                if (new Chronos($ticket->last_ticket_date) > $date->modify('-1 hour')) {
                    // Delete the original message from the author.
                    $wrapper->Discord->getLoop()->addTimer(5, function () use ($wrapper) {
                        return $wrapper->Message->delete();
                    });

                    $wrapper->Message
                        ->reply("Vous avez déjà ouvert un ticket il y a moins d'une heure, merci de patienter ".
                        "avant d'en ouvrir un nouveau.")
                        ->then(function ($message) use ($wrapper) {
                            $wrapper->Discord->getLoop()->addTimer(8, function () use ($message) {
                                return $message->delete();
                            });
                        });

                    return;
                }

                // Select the setting from the database.
                $setting = $wrapper->API->setting()->get('ticket.number');

                $ticketNumber = 1;

                if (!is_null($setting)) {
                    $ticketNumber = $setting->value + 1;
                }

                $channel = $wrapper->Guild->channels->create([
                    'name' =>Configure::read('Discord.ticket.prefix') . sprintf("%04d", $ticketNumber),
                    'topic' => 'Ticket de ' . $wrapper->Message->author->username,
                    'type' => Channel::TYPE_TEXT,
                    'parent_id' => Configure::read('Discord.ticket.category'),
                    'permission_overwrites' => [
                        [
                            'id' => Configure::read('Discord.guild'), // Everyone
                            'type' => 0,
                            'deny' => 0x400
                        ],
                        [
                            'id' => $userId,
                            'type' => 1,
                            'allow' => 0x400 |  0x800 | 0x10000,
                            'deny' => 0x40
                        ],
                        [
                            'id' => '386617196258000896', // Admin
                            'type' => 0,
                            'allow' => 0x8
                        ],
                        [
                            'id' => '511657672987377685', // Ambassadeur
                            'type' => 0,
                            'allow' => 0x8
                        ]
                    ]
                ]);
                $wrapper->Guild->channels->save($channel)
                    ->then(function (Channel $channel) use ($wrapper, $message, $ticketNumber, $ticket) {
                        // Delete the message from the user.
                        $wrapper->Discord->getLoop()->addTimer(3, function () use ($wrapper) {
                            return $wrapper->Message->delete();
                        });

                        // Prepare the embed to send to the news channel.
                        $embed = new Embed($wrapper->Discord);
                        $embed
                            ->setColor(hexdec("1DFCEA"))
                            ->setDescription(Configure::read('Discord.ticket.ticket_embed.embed_text'))
                            ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                        $messageText = Configure::read('Discord.ticket.ticket_embed.message_text');

                        if (Configure::read('dev')) {
                            $messageText = Configure::read('Discord.ticket.ticket_embed.message_text_dev');
                        }

                        // Send the message in the new channel using MessageBuilder
                        $messageBuilder = MessageBuilder::new()
                            ->setContent(sprintf($messageText, $wrapper->Message->author))
                            ->addEmbed($embed);

                        $channel
                            ->sendMessage($messageBuilder)
                            ->then(function ($message) use ($wrapper, $ticketNumber, $ticket) {
                                // Update the setting in the database.
                                $wrapper->API->setting()->update([
                                    'name' => 'ticket.number',
                                    'value' => $ticketNumber
                                    ]);

                                $data = [
                                    'ticket_count' => $ticket->ticket_count + 1,
                                    'ticket_opened' => $ticket->ticket_opened + 1,
                                    'last_ticket_date' => (string)Chronos::now(),
                                    'ticket_message_id' => $message->id
                                ];

                                // Update the ticket in the database.
                                $wrapper->API->ticket()->update($ticket->discord_id, $data);
                            });
                    });
                break;

            case 'add':
                //  Remove all the non alphanumeric characters.
                $userId = preg_replace('/\D/', '', $message['arguments'][1]);

                $wrapper->Members->fetch($userId)
                    ->then(function ($member) use ($wrapper) {
                        $wrapper->Message->channel->setPermissions($member, [
                            'view_channel',
                            'send_messages',
                            'read_message_history'
                        ], [
                            'add_reactions',
                        ])->then(function () use ($wrapper, $member) {
                            $wrapper->Message->reply("Les permissions de ticket on bien été appliqué à $member.")
                                ->then(function ($message) use ($wrapper) {
                                    $wrapper->Message->delete();
                                    // Delete the previous message.
                                    $wrapper->Discord->getLoop()->addTimer(6, function () use ($message) {
                                        $message->delete();
                                    });
                                });
                        });
                    });
                break;

            case 'remove':
                //  Remove all the non alphanumeric characters.
                $userId = preg_replace('/\D/', '', $message['arguments'][1]);

                $wrapper->Members->fetch($userId)
                    ->then(function ($member) use ($wrapper) {
                        $wrapper->Message->channel->setPermissions($member, [], [
                            'view_channel',
                        ])->then(function () use ($wrapper, $member) {
                            $wrapper->Message
                                ->reply("Les permissions de ticket on bien été appliqué à $member.")
                                ->then(function ($message) use ($wrapper) {
                                    $wrapper->Message->delete();
                                    // Delete the previous message.
                                    $wrapper->Discord->getLoop()->addTimer(6, function () use ($message) {
                                        $message->delete();
                                    });
                                });
                        });
                    });
                break;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param \Discord\Parts\WebSockets\MessageReaction $reaction The reaction used.
     *
     * @return void
     */
    public function onMessageReactionAdd(Wrapper $wrapper, MessageReaction $reaction): void
    {
         // Get the channel from the reaction
        $wrapper->Guild->channels->fetch($reaction->channel_id)
            ->then(function ($channel) use ($wrapper, $reaction) {
                $prefix = substr($channel->name, 0, strlen(Configure::read('Discord.ticket.prefix')));

                // Check if the reaction is added in a ticket channel.
                if ($prefix != Configure::read('Discord.ticket.prefix')) {
                    return;
                }

                // Select the user from the database.
                $ticket = $wrapper->API->ticket()->getByTicketMessage($reaction->message->id);

                if (is_null($ticket)) {
                    return;
                }

                $wrapper->Members->fetch($ticket->discord_id)
                    ->then(function ($member) use ($wrapper, $ticket, $reaction, $channel) {
                        // Send the private message to the user
                        $member->user->getPrivateChannel()->then(function (Channel $dmChannel) use ($wrapper, $reaction) {
                            $embed = new Embed($wrapper->Discord);
                            $embed
                                ->setColor(hexdec("1DFCEA"))
                                ->setDescription(sprintf(
                                    Configure::read('Discord.ticket.message_dm'),
                                    substr($reaction->message->channel->name, -4, 4)
                                ))
                                ->setImage('https://i.imgur.com/m18sQGA.png')
                                ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                            $dmChannel->sendEmbed($embed);
                        });

                        // Log the closed ticket in logs-bot.
                        $wrapper->Guild->channels->fetch(Configure::read('Discord.channels.logs-bot'))
                            ->then(function ($logChannel) use ($wrapper, $ticket, $reaction) {
                                $embed = new Embed($wrapper->Discord);
                                $embed
                                    ->setColor(hexdec("1DFCEA"))
                                    ->setDescription(sprintf(
                                        "<@%s> vient de fermer le Ticket N°`%s` ouvert par <@%s>",
                                        $reaction->user_id,
                                        substr($reaction->message->channel->name, -4, 4),
                                        $ticket->discord_id
                                    ))
                                    ->setAuthor(
                                        'Ticket System',
                                        'https://images-ext-2.discordapp.net/external/'.
                                        'iRFWZax-j4sQGbZSg9tbK4bS7NNqRlPkR3Kv1YB55jc/https/tickettool.xyz/images/footer.png'
                                    )
                                    ->setThumbnail('https://cdn.discordapp.com/app-icons/635391187301433380/'.
                                    '1816aec0f6a4418f7ed19773e97dfb98.png');

                                $logChannel->sendEmbed($embed);
                            });

                        // Delete the channel ticket-XXXX.
                        $wrapper->Guild->channels->delete($channel)
                            ->then(function ($channel) use ($wrapper, $ticket) {
                                // Update the table in database.
                                $data = [
                                    'ticket_count' => $ticket->ticket_count,
                                    'ticket_opened' => 0,
                                    'last_ticket_date' => $ticket->last_ticket_date,
                                    'ticket_message_id' => null
                                ];

                                // Update the ticket in the database.
                                $wrapper->API->ticket()->update($ticket->discord_id, $data);
                            });
                    });
            });
    }
}
