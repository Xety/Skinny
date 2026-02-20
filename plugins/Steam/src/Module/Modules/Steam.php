<?php
namespace Steam\Module\Modules;

use Cake\Chronos\Chronos;
use DateTime;
use Discord\Parts\Embed\Embed;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;

class Steam implements ModuleInterface
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
            case 'steamban':
                // Get the steam ID.
                $steamId = $message['arguments'][0];

                // Check if the ban is forever.
                $forever = 0;
                if ($message['arguments'][1] == 0) {
                    $forever = 1;
                }

                // If not forever, then get the hours.
                $hours = 0;
                if (!$forever) {
                    $hours = $message['arguments'][1];
                }

                // Get the reason.
                $reason = trim(strstr(trim(strstr($message['message'], " ")), " "));

                // Select the steam from the database.
                $steamBan = $wrapper->API->steamBan()->get($steamId);

                // Check if the steam ID is not already perm banned
                if ((!is_null($steamBan) && $steamBan->forever == true) || ($steamBan && $steamBan->expire_at > Chronos::now())) {
                    $embed = new Embed($wrapper->Discord);
                    $embed
                        ->setColor(hexdec("FF0000"))
                        ->addField([
                            'name' => '**` Banni par `**',
                            'value' => "<@{$steamBan->banned_by}>",
                            'inline' => true
                        ]);

                    // Check if the ban is perm
                    if ($steamBan->forever == true) {
                        $embed->addField([
                                'name' => '**` Durée restante `**',
                                'value' => "Permanant",
                                'inline' => true
                            ]);
                    } else {
                        $date = new Chronos($steamBan->expire_at);
                        $embed->addField([
                            'name' => '**` Durée restante `**',
                            'value' => $date->diffInHours() . ' heure(s)',
                            'inline' => true
                        ]);
                    }

                    $embed
                        ->addField(
                            [
                                'name' => '**` Date du bannissement `**',
                                'value' => (new DateTime($steamBan->created_at))->format('d-m-Y à H:i:s'),
                                'inline' => true
                            ],
                            [
                                'name' => '**` Raison `**',
                                'value' => $steamBan->reason,
                                'inline' => false
                            ]
                        )
                        ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendMessage(
                        "<@{$wrapper->Message->author->id}>, Ce Steam ID est déjà banni :",
                        false,
                        $embed
                    );

                    return;
                }

                // Insert values in database.
                $date = new Chronos();
                $date = $date->modify('+' . $hours . 'hours');
                $author = $wrapper->Message->author->id;

                $values = [
                    'steam_id' => $steamId,
                    'banned_by' => $author,
                    'forever' => (bool)$forever,
                    'reason' => $reason,
                    'expire_at' => (string)$date,
                ];

                // Insert the new SteamBan in database.
                $wrapper->API->steamBan()->create($values);

                // Reply to the author
                $embed = new Embed($wrapper->Discord);
                $embed
                    ->setColor(hexdec("FF0000"))
                    ->addField([
                        'name' => '**` Banni par `**',
                        'value' => "<@{$author}>",
                        'inline' => true
                    ]);

                if ($forever == 1) {
                    $embed->addField([
                        'name' => '**` Durée restante `**',
                        'value' => "Permanant",
                        'inline' => true
                    ]);
                } else {
                    $date = new Chronos($date);
                    $embed->addField([
                        'name' => '**` Durée restante `**',
                        'value' => $date->diffInHours() . ' heure(s)',
                        'inline' => true
                    ]);
                }

                $embed
                    ->addField(
                        [
                            'name' => '**` Date du bannissement `**',
                            'value' => (new DateTime(Chronos::now()))->format('d-m-Y à H:i:s'),
                            'inline' => true
                        ],
                        [
                            'name' => '**` Raison `**',
                            'value' => $reason,
                            'inline' => false
                        ]
                    )
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                $wrapper->Message->channel->sendMessage(
                    "<@{$wrapper->Message->author->id}>, Ce Steam ID vient d'être banni :",
                    false,
                    $embed
                );
                break;

            case 'steamcheck':
                // Get the steam ID.
                $steamId = $message['arguments'][0];

                // Select the steam from the database.
                $steamBan = $wrapper->API->steamBan()->checkBan($steamId);

                if (is_null($steamBan)) {
                    $wrapper->Message->reply('Ce Steam ID n\'est pas banni de nos serveurs.');

                    return;
                }

                $embed = new Embed($wrapper->Discord);
                $embed
                    ->setColor(hexdec("FF0000"))
                    ->addField([
                        'name' => '**` Banni par `**',
                        'value' => "<@{$steamBan->banned_by}>",
                        'inline' => true
                    ]);

                if ($steamBan->forever == true) {
                    $embed->addField([
                        'name' => '**` Durée restante `**',
                        'value' => "Permanant",
                        'inline' => true
                    ]);
                } else {
                    $date = new Chronos($steamBan->expire_at);
                    $embed->addField([
                        'name' => '**` Durée restante `**',
                        'value' => $date->diffInHours() . ' heure(s)',
                        'inline' => true
                    ]);
                }

                $embed
                    ->addField(
                        [
                            'name' => '**` Date du bannissement `**',
                            'value' => (new DateTime($steamBan->created_at))->format('d-m-Y à H:i:s'),
                            'inline' => true
                        ],
                        [
                            'name' => '**` Raison `**',
                            'value' => $steamBan->reason,
                            'inline' => false
                        ]
                    )
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                $wrapper->Message->channel->sendMessage(
                    "<@{$wrapper->Message->author->id}>, Ce Steam ID est banni de nos serveurs :",
                    false,
                    $embed
                );
                break;
        }
    }
}
