<?php
namespace Dons\Module\Modules;

use Cake\Chronos\Chronos;
use Cake\Chronos\Date;
use DateTime;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;
use Skinny\Utility\Command;
use Skinny\Utility\User;

class Dons implements ModuleInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param array $message The message array.
     *
     * @return void
     */
    public function onCommandMessage(Wrapper $wrapper, $message): void
    {
        if (count($message['arguments']) < Configure::read('Commands.' . $message['command'] . '.params')) {
            $wrapper->Message->reply(Command::syntax($message));

            return;
        }

        // Handle the command.
        switch ($message['command']) {
            case 'fait':
                $type = strtolower($message['arguments'][0]);

                if ($type == 'skin') {
                    $type = $type;
                } elseif ($type == 'couleur') {
                    $type = 'color';
                } else {
                    $wrapper->Message->reply('Ce type est inconnu. Types possibles : [` Skin ` | ` Couleur `]');
                    return;
                }
                //  Remove all the non alphanumeric characters.
                $userId = preg_replace('/\D/', '', $message['arguments'][1]);

                // Select the user from the database.
                $user = $wrapper->API->user()->getByDiscord($userId);

                // Check if the user exist and if he still have a skin available.
                if (is_null($user) || $user->{$type . '_remain'} < 1) {
                    $wrapper->Message->reply(sprintf(Configure::read('language.' . $type .'_done_error')));
                } else {
                    $count = $user->{$type . '_remain'} - 1;
                    $data = [
                        $type . '_remain' => $count
                    ];
                    $wrapper->API->user()->updateByDiscord($userId, $data);

                    $wrapper->Message->reply(sprintf(
                        Configure::read('language.' . $type . '_done_message'),
                        $userId,
                        $count
                    ));
                }
                //$fluent->close();
                break;

            case 'demande':
                /*$type = strtolower($message['arguments'][0]);

                if ($type == 'skin') {
                    $type = $type;
                } elseif ($type == 'couleur') {
                    $type = 'color';
                } else {
                    $wrapper->Message->reply('Ce type est inconnu. Types possibles : [` Skin ` | ` Couleur `]');
                    return;
                }
                $userId = $wrapper->Message->author->id;

                // Select the user from the database.
                $user = $wrapper->API->user()->getByDiscord($userId);

                // Check if the user exist and if he still have a color available.
                if (is_null($user) || $user->{$type . '_remain'} < 1) {
                    $wrapper->Message->reply(sprintf(Configure::read('language.no_more_' . $type)));
                } else {
                    // Check if the user haven't asked a color in the last X hours.
                    $lastAskDate = $user->{$type . '_asked_at'};

                    // Must counter the NULL value else we can't instance Chronos with NULL value.
                    $lastAskDate = $lastAskDate ?? Chronos::now(
                        '-' . (Configure::read('Dons.interval_between_asking_' . $type) + 3) . ' hours'
                    );
                    $date = new Chronos($lastAskDate);
                    $date = $date ->modify('+' . Configure::read('Dons.interval_between_asking_' . $type) . 'hours');

                    if (Chronos::now() < $date) {
                        $wrapper->Message->reply(sprintf(
                            Configure::read('language.interval_between_asking_' . $type),
                            Configure::read('Dons.interval_between_asking_' . $type)
                        ));

                        break;
                    }
                    // Update the latest asked color date.
                    $data = [
                        $type . '_asked_at' => (string)Chronos::now()
                    ];
                    $wrapper->API->user()->updateByDiscord($userId, $data);

                    $channel = $wrapper->Guild->channels->fetch(Configure::read('Discord.channels.logs-bot'))
                        ->then(function ($channel) use ($wrapper, $userId, $type) {
                            $admins = '<@&652646216806432772>';

                            $channel->sendMessage(sprintf(
                                Configure::read('language.asked_' . $type . '_admin_message'),
                                $admins,
                                $userId
                            ));
                            $wrapper->Message->reply(Configure::read('language.asked_' . $type . '_reponse_message'));
                        });
                }*/

                $wrapper->Message->reply('Cette commande n\'existe plus, veuillez utiliser le système de ticket'.
                ' (<#735592989551886407>) pour faire votre demande de couleur / skin.');
                break;

            case 'inventory':
            case 'inventaire':
            case 'check':
                $userId = $wrapper->Message->author->id;

                $checkUser = (isset($message['arguments'][0]) && !empty($message['arguments'][0])) ? true : false;

                // If he specified a user, check if he have the permission to check an user's inventory.
                if ($checkUser) {
                    if (!User::hasPermission($wrapper, Configure::read('Discord.admins'))) {
                        $wrapper->Message->reply(Configure::read('language.inventory_not_allowed'));

                        break;
                    }
                    //  Remove all the non alphanumeric characters.
                    $userId = preg_replace('/\D/', '', $message['arguments'][0]);
                }
                // Select the user from the database.
                $user = $wrapper->API->user()->getByDiscord($userId);

                // Check if the user exist.
                if (is_null($user)) {
                    if ($checkUser) {
                        $wrapper->Message->reply(sprintf(Configure::read('language.member_not_allowed_admin')));
                    } else {
                        $wrapper->Message->reply(sprintf(Configure::read('language.member_not_allowed')));
                    }
                } else {
                    if (!isset($message['arguments'][0])) {
                        $wrapper->Message->reply(sprintf(
                            Configure::read('language.your_inventory'),
                            $user->color_remain,
                            $user->skin_remain,
                            $user->reward_count
                        ));
                    } else {
                        $wrapper->Message->reply(sprintf(
                            Configure::read('language.your_inventory_admin'),
                            '<@' . $userId . '>',
                            $user->color_remain,
                            $user->skin_remain,
                            $user->reward_count
                        ));
                    }
                }
                break;
        }
    }
}
