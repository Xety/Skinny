<?php
namespace Admin\Module\Modules;

use Cake\Chronos\Chronos;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use DateTime;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;
use Skinny\Rcon\Rcon;

class Admin implements ModuleInterface
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
        //Handle the command.
        switch ($message['command']) {
            case 'info':
                // This command can only be send in Admins's channels.
                if (!in_array($wrapper->Message->channel->id, Configure::read('Discord.channels.admins'))) {
                    $channels = "";

                    foreach (Configure::read('Discord.channels.admins') as $channel) {
                        $channels .= "<#" . $channel . ">";
                    }
                    $wrapper->Message->reply(
                        "Cette commande peut uniquement être utilisée dans ces channels : " . $channels
                    );

                    return;
                }

                //  Remove all the non alphanumeric characters.
                $userId = preg_replace('/\D/', '', $message['arguments'][0]);

                $user = $wrapper->API->user()->getByDiscord($userId);

                // Check that the user exist in database.
                if (is_null($user)) {
                    $wrapper->Message->reply(
                        "Aucune données n'a été trouvée pour cet utilisateur."
                    );

                    return;
                }

                $paypal = $wrapper->API->paypal()->getByUser($user->id);

                $wrapper->Members->fetch($userId)
                    ->then(function ($member) use ($wrapper, $user, $paypal) {

                        $embed = new Embed($wrapper->Discord);
                        $embed
                            ->setColor(hexdec("1DFCEA"))
                            ->setDescription("**Voici les information de <@{$member->id}> ".
                            "({$member->username}) : \n\n**")
                            ->addField(
                                [
                                    'name' => "\n\n.",
                                    'value' => '**Catégorie Général**',
                                    'inline' => true
                                ],
                                [
                                    'name' => '**Inscription Discord**',
                                    'value' => 'Le ' . \Skinny\Utility\Snowflake::deconstruct($member->id)->date->format('d-m-Y à H:i:s'),
                                    'inline' => false
                                ],
                                [
                                    'name' => '**Rejoint notre Discord**',
                                    'value' => 'Le ' . (new DateTime($member->joined_at))->format('d-m-Y à H:i:s'),
                                    'inline' => false
                                ],
                            )
                            ->setImage('https://i.imgur.com/m18sQGA.png');

                        if (!is_null($user)) {
                            $embed
                                ->addField(
                                    [
                                        'name' => "\n\n.",
                                        'value' => '** Catégorie <@&386617500516876289>**',
                                        'inline' => true
                                    ],
                                    [
                                        'name' => '**Membre depuis**',
                                        'value' => 'Le ' . (new DateTime($user->created_at))->format('d-m-Y à H:i:s'),
                                        'inline' => false
                                    ],
                                    [
                                        'name' => '**Fin d\'abonnement**',
                                        'value' => 'Le ' . (new DateTime($user->member_expire_at))->format('d-m-Y à H:i:s'),
                                        'inline' => false
                                    ],
                                    [
                                        'name' => '**Nombre de Donations**',
                                        'value' => $user->transaction_count,
                                        'inline' => false
                                    ],
                                    [
                                        'name' => '**Total d\'argent donné **',
                                        'value' => (!is_null($paypal) ? $paypal->amount_total : '0') . '€',
                                        'inline' => false
                                    ],
                                    [
                                        'name' => '**Skin restants **',
                                        'value' => $user->skin_remain,
                                        'inline' => false
                                    ],
                                    [
                                        'name' => '**Couleurs restants **',
                                        'value' => $user->color_remain,
                                        'inline' => false
                                    ],
                                );
                        } else {
                            $embed
                                ->addField([
                                    'name' => "\n\n.",
                                    'value' => 'Ce membre n\'a jamais fait de donation.',
                                    'inline' => true
                                ]);
                        }

                        $transactions = $wrapper->API->transaction()->getByUser($user->id);

                        if (!is_null($transactions)) {
                            $embed->addField([
                                'name' => "\n\n.",
                                'value' => '** Transactions Paypal **',
                                'inline' => true
                            ]);

                            foreach ($transactions as $transaction) {
                                $embed->addField([
                                    'name' => "**{$transaction->amount} {$transaction->currency}**",
                                    'value' => "Paiement ID : **{$transaction->payment_id}**",
                                    'inline' => false
                                ]);
                            }
                        }

                        $embed->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                        $wrapper->Message->channel->sendEmbed($embed);
                    });
                break;

            case 'tribu':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_red_triangle:  ━━━  Bonjour à toi survivant(e) de l'arche   ━━━  :small_red_triangle:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("\n```yaml\n- Ce salon est réservé aux joueurs(euses) du cluster Division\n- Pour faire ta demande, donne nous quelques infos du genre```\n> Sur quelles maps tu es installé(e)\n> Tes compétences de jeu\n> Tes disponibilités\n> Etc...\n\n__***Et n'oublie pas de lire ceci:***__\n> <#564069972956151818>\n> <#466527538974556160>\n\n:globe_with_meridians:   -  ***[ARK Division France](https://ark-division.fr/ \"Notre Site Internet\")***")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed)
                        ->then(function (Message $message) use ($wrapper) {
                            $wrapper->Message->delete();
                        });
                break;

            case 'aideadmin':
                // This command can only be send in Admins's channels.
                if (!in_array($wrapper->Message->channel->id, Configure::read('Discord.channels.admins'))) {
                    $channels = "";

                    foreach (Configure::read('Discord.channels.admins') as $channel) {
                        $channels .= "<#" . $channel . ">";
                    }
                    $wrapper->Message->reply(
                        "Cette commande peut uniquement être utilisée dans ces channels : " . $channels
                    );

                    return;
                }

                $command = Configure::read('Command.prefix');

                $embed = new Embed($wrapper->Discord);
                $embed
                    ->setTitle("**Les commandes programmées pour les Admins ARK DIVISION**")
                    ->setColor(hexdec("FF0000"))
                    ->setDescription("\n:small_blue_diamond: : Commandes <@&386617196258000896>".
                    "\n:small_red_triangle:  : Commandes <@&652648370867011635>")
                    ->addField(
                        [
                            'name' => sprintf(":small_blue_diamond:  **`%sinfo [Membre]`**", $command),
                            'value' => 'Permet de voir les informations d\'un membre.',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_blue_diamond:  **`%sfait [Skin | Couleur]`**", $command),
                            'value' => 'Permet d\'indiquer au bot qu\'une couleur/skin a été fait(e).',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_blue_diamond: **`%sban [Membre]`**", $command),
                            'value' => 'Permet de bannir un membre.',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_blue_diamond: **`%stribu`**", $command),
                            'value' => 'Demande d\'informations pour les personnes recherchant une tribu.',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_blue_diamond: **`%ssteamban [SteamID] [Durée ".
                            "(Heures) (0 Perm)] [Raison]`**", $command),
                            'value' => 'Ajoute le steam ID dans la banlist. (Un <@&652647274681335860> '.
                            'doit l\'ajouter au Pastebin)',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_blue_diamond: **`%ssteamcheck [SteamID]`**", $command),
                            'value' => 'Permet de vérifier si un steam ID est banni.',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_blue_diamond: **`%sticket add [Membre]`**", $command),
                            'value' => 'Permet d\'ajouter un membre dans un ticket.',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_blue_diamond: **`%sticket remove [Membre]`**", $command),
                            'value' => 'Permet de supprimer un membre d\'un ticket.',
                            'inline' => true
                        ]
                    )
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'say':
                $wrapper->Message->delete();
                $wrapper->Message->channel->sendMessage($message['message']);
                break;
        }
    }
}
