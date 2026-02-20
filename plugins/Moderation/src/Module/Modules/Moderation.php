<?php
namespace Moderation\Module\Modules;

use Discord\Parts\Channel\Message;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;
use Skinny\Utility\User;

class Moderation implements ModuleInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param array $message The message array.
     *
     * @return void
     */
    public function moderationReglementDiscord(Wrapper $wrapper, $message): void
    {
        // Match the "ok" word in the text.
        $matchs = preg_match('/^(ok)$/i', $message['parts'][0]);

        //
        if ($wrapper->Message->id != '743573359433875587' &&
            $wrapper->Message->author->user->bot == null &&
            // Check message matchs
            !$matchs &&
            // Check admins users
            !(!is_null($wrapper->Message->author) &&
                in_array(
                    User::getHighestRole($wrapper->Message->author->roles->toArray()),
                    Configure::read('Discord.admins')
                )
            )
        ) {
            // Delete the originale message from the author.
            $wrapper->Discord->getLoop()->addTimer(2, function () use ($wrapper) {
                return $wrapper->Message->delete();
            });

            // Reply to the author and then delete this same mesage.
            $wrapper->Message
                ->reply('Vous n\'êtes pas autorisé à parler dans ce channel sauf pour valider le règlement')
                ->then(function ($message) use ($wrapper) {
                    $wrapper->Discord->getLoop()->addTimer(6, function () use ($message) {
                        $message->delete();
                    });
                });

            return;
        }

        // Check if the user has not already the role
        if ($wrapper->Message->author->roles->has(Configure::read('Discord.roles.valider'))) {
            $wrapper->Message->delete();

            $wrapper->Message
                ->reply('Vous avez déjà le role.')
                ->then(function ($message) use ($wrapper) {
                    // Delete the bot message.
                    $wrapper->Discord->getLoop()->addTimer(6, function () use ($message) {
                        $message->delete();
                    });
                });

                return;
        }

        // Add the raction to the message, then delete his message after 2 seconds
        $wrapper->Message->react('✅');

        $OldMessage = $wrapper->Message;

        // Delete the original message from the author.
        $wrapper->Discord->getLoop()->addTimer(4, function () use ($OldMessage) {
            return $OldMessage->delete();
        });

        // Add the role Valider to the user.
        $wrapper->Message->author->addRole(Configure::read('Discord.roles.valider'));

        // Reply to the user
        $wrapper->Message
            ->reply('Le rôle Validé va vous être attribué, **merci d\'avoir lu et approuvé le règlement**.')
            ->then(function (Message $message) use ($wrapper) {
                // Delete the bot message.
                $wrapper->Discord->getLoop()->addTimer(6, function () use ($message) {
                    return $message->delete();
                });
            });
    }

    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param array $message The message array.
     *
     * @return void
     */
    public function moderationSupportAdmin(Wrapper $wrapper, $message): void
    {
        if ($wrapper->Message->id != '735593708208259092' &&
            $wrapper->Message->author->bot == null &&
            $message['parts'][0] != '!ticket' &&
            $message['parts'][0] != '-ticket'
        ) {
            // Delete the original message from the author.
            $wrapper->Discord->getLoop()->addTimer(2, function () use ($wrapper) {
                return $wrapper->Message->delete();
            });

            // Reply to the author and then delete this same mesage.
            $wrapper->Message
                ->reply('Vous n\'êtes pas autorisé à parler dans ce channel sauf pour utiliser la '.
                    'commande `-ticket open`.')
                ->then(function ($message) use ($wrapper) {
                    $wrapper->Discord->getLoop()->addTimer(6, function () use ($message) {
                        return $message->delete();
                    });
                });

            return;
        }
    }
}
