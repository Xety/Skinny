<?php
namespace Developer\Module\Modules;

use DateTime;
use Discord\Parts\Embed\Embed;
use Skinny\Api\Api;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;
use Skinny\Rcon\Rcon;
use Skinny\Utility\User;

class Developer implements ModuleInterface
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
        //Handle the command.
        switch ($message['command']) {
            case 'memoire':
                $memoryKo = round(memory_get_usage() / 1024);
                $memoryMo = number_format($memoryKo / 1024, 1, ',', ' ');

                $wrapper->Message->reply('Mémoire utilisée : `' . $memoryKo . 'Ko` (`' . $memoryMo . 'Mo`).');

                break;

            case 'version':
                $wrapper->Message->reply('La version actuelle est : ' . Configure::version());
                break;

            case 'time':
                $seconds = floor(microtime(true) - TIME_START);
                $start = new DateTime("@0");
                $end = new DateTime("@$seconds");
                $wrapper->Message->reply(
                    'I\'m running since ' . $start->diff($end)->format('%a days, %h hours, %i minutes and %s seconds.')
                );

                break;

            case 'updatecommandes':
                $command = Configure::read('Command.prefix');

                $embeds = [];

                $embed = new Embed($wrapper->Discord);
                $embed
                    ->setDescription("**Les commandes <@&386615163165605889>\n \n**")
                    ->setColor(hexdec("7c7c7c"))
                    ->addField(
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sserveur | %sip`**", $command, $command),
                            'value' => 'Affiche la liste de tous les serveurs',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sfavoris`**", $command),
                            'value' => 'Affiche un tutoriel pour ajouter un serveur en favoris sur Steam',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%srate`**", $command),
                            'value' => 'Affiche les réglages des serveurs',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%snew`**", $command),
                            'value' => 'Vous donne accès à un tutoriel pour débutant sur ARK',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sinfodon`**", $command),
                            'value' => 'Vous informe sur les avantages d\'une donation',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%smod`**", $command),
                            'value' => 'Affiche la liste des mods',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%srepro`**", $command),
                            'value' => 'Affiche le tutoriel pour la reproduction des dinos',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%skibble`**", $command),
                            'value' => 'Affiche les spécificitées pour la fabrication des kibbles',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sdiscord`**", $command),
                            'value' => 'Affiche un message d\'avertissement',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sbug`**", $command),
                            'value' => 'Affiche la liste de possibles bugs ainsi que leurs résolutions',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sdlc`**", $command),
                            'value' => 'Affiche le tutoriel pour l\'installation des DLC de ARK',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sadmin`**", $command),
                            'value' => 'Affiche la liste des catégories d\'Admin de ARK Division',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sregles`**", $command),
                            'value' => 'Affiche les 2 channels de règlement serveur et discord',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sdepop`**", $command),
                            'value' => 'Affiche les timers de depop',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%slien`**", $command),
                            'value' => 'Affiche les réseaux sociaux de la communauté',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%stuto`**", $command),
                            'value' => 'Affiche la liste de nos tutoriels',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%scluster`**", $command),
                            'value' => 'Explique l\'intérêt du Cluster',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sarcbar`**", $command),
                            'value' => 'Permet d\'expliquer les ARc Bar disponibles sur les serveurs via TCs',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sticket open`**", $command),
                            'value' => 'Permet d\'ouvrir un ticket via le channel <#735592989551886407>',
                            'inline' => true
                        ],
                        [
                            'name' => sprintf(":small_orange_diamond:  **`%sversionmod`**", $command),
                            'value' => 'Affiche un tuto pour résoudre le problème de version des mods.',
                            'inline' => true
                        ]
                    );

                $embedMembre = new Embed($wrapper->Discord);
                $embedMembre
                    ->setDescription("**Les commandes <@&386617500516876289>\n \n**")
                    ->setColor(hexdec("00af94"))
                    ->addField(
                        [
                            'name' => sprintf(":small_blue_diamond: **`%sinventaire Option : [Membre]`**", $command),
                            'value' => '*(L\'option **Membre** est réservée aux <@&386617196258000896>)* Permet de'.
                            ' voir son inventaire',
                            'inline' => true
                        ]
                    )
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');


                $channel = $wrapper->Guild->channels->fetch(Configure::read('Discord.commands.channel_id'))
                        ->then(function ($channel) use ($wrapper, $embed, $embedMembre) {

                            $channel->messages->fetch(Configure::read('Discord.commands.message_id_everyone'))
                                ->then(function ($message) use ($channel, $embed) {
                                    $channel->editMessage($message, '', false, $embed);
                                });

                            $channel->messages->fetch(Configure::read('Discord.commands.message_id_membre'))
                                ->then(function ($message) use ($channel, $embedMembre) {
                                    $channel->editMessage($message, '', false, $embedMembre);
                                });
                        });

                $wrapper->Message->reply('Commandes mise à jour !');
                break;

            case 'spyplayer':
                $servers = $wrapper->API->server()->getAll();

                $serversChecked = 0;

                foreach ($servers as $server) {
                    $rcon = new Rcon($server->ip, $server->rcon_port, $server->password, 10);

                    // Get the connection if it is etablished or not.
                    if (!$rcon->connect()) {
                        // Update the number of servers.
                        $serversChecked++;

                        continue;
                    }

                    //  Remove all the non alphanumeric characters.
                    $steamId = preg_replace('/\D/', '', $message['arguments'][0]);

                    $tribeId = $this->sendCommand($server, "GetTribeIdOfPlayer " . $steamId);
                    $tribeId = trim(preg_replace('/[^0-9]/', '', $tribeId));

                    debug($server->name);

                    debug($tribeId);

                    if (empty($tribeId)) {
                        $dinos = $this->sendCommand($server, "ListTribeDinos " . $tribeId);
                        $dinos = array_map('trim', explode("\n", trim($dinos)));

                        debug($dinos);
                    }

                    $dinosPlayer = $this->sendCommand($server, "ListPlayerDinos " . $steamId);
                    $dinosPlayer = array_map('trim', explode("\n", trim($dinosPlayer)));

                    debug($dinosPlayer);

                    $serversChecked++;
                }

                debug($serversChecked);

                break;
        }
    }

    /**
     * Connect to RCON, send the command and return the response.
     *
     * @param string $command The command tos end to the RCON server.
     *
     * @return string
     */
    protected function sendCommand($server, $command): string
    {
        $rcon = new Rcon($server->ip, $server->rcon_port, $server->password, 3);

        $rcon->connect();

        $response = $rcon->sendCommand($command);

        $rcon->disconnect();

        return $response;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param array $message The message array.
     *
     * @return void
     */
    public function onChannelMessage(Wrapper $wrapper, $message): void
    {
        // To avoid infinite loop with the other bot
        if (Configure::read('debug') == true) {
            return;
        }

        // Check blacklisted channels
        if (in_array($wrapper->Message->channel_id, Configure::read('Discord.channels.blacklisted'))) {
            return;
        }

        // Check admins users
        $member = $wrapper->Message->author;

        if (!is_null($member) &&
            !is_null($member->roles) &&
            in_array(User::getHighestRole($member->roles->toArray()), Configure::read('Discord.admins'))
        ) {
            return;
        }

        //Handle the aide/help commande
        if (preg_match("#\b(aide|help)\b#", $message['raw'])) {
            $wrapper->Message->reply("Si vous souhaitez avoir de l'aide, n'hésitez pas à visiter le ".
            "channel <#738230751748685844> pour la liste des commandes.");
        }

        //Handle the admin/dispo commande
        if (preg_match("#\b(admin)\b#", $message['raw']) && preg_match("#\b(dispo)\b#", $message['raw'])) {
            $wrapper->Message->reply("Si vous souhaitez avoir de l'aide de la part d'un admin, n'hésitez ".
            "pas à utiliser la commande `-admin` pour avoir la liste de tous les admins et leurs rôles. ".
            "Pour une aide plus rapide, n'hésitez pas à mentionner le rôle qui correspond à votre problème.");
        }

        //Handle the rejoindre/serveur commande
        if (preg_match("#\b(rejoindre)\b#", $message['raw']) &&
            preg_match("#\b(serveur|server|serveurs)\b#", $message['raw'])
        ) {
            $wrapper->Message->reply("Si vous souhaitez avoir de l'aide pour rejoindre un serveur, ".
            "utiliser la commande `-favoris` pour savoir comment mettre en favoris les serveurs.".
            " Pour connaître la liste des serveurs utilisez la commande `-serveur`. Enfin pour toute".
            " autre aide, vous pouvez utilisez la commande `-aide`.");
        }
    }
}
