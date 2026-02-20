<?php
namespace Member\Module\Modules;

use Cake\Chronos\Chronos;
use DateTime;
use Discord\Parts\User\Member as PartMember;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;

class Member implements ModuleInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param \Discord\Parts\User\Member $member The member that joined the guild.
     *
     * @return void
     */
    public function onGuildMemberAdd(Wrapper $wrapper, PartMember $member): void
    {
        if (Configure::read('debug') == true) {
            return;
        }

        $text =  <<<TEXT
        Hey <@%s>, bienvenue sur **ARK Division France**:tada::hugging: !
        > Lis attentivement le message dans <#564069972956151818>, il contient les règles à obligatoirement suivre sur notre discord.
        > Valide ensuite ta plateforme de jeu ici **<#723876340641562794>** pour être orienté vers les bons salons.
        ```fix
        - Tout démarchage ou pub pour un autre serveur que celui de DIVISION est interdit/banni
        - La non lecture de ce règlement, ou message ne le respectant pas, sera automatiquement suivi d'un bannissement sans avertissement !
        ```
        TEXT;

        $wrapper->Guild->channels->fetch(Configure::read('Discord.channels.welcome'))
            ->then(function ($channel) use ($text, $member) {
                $channel->sendMessage(sprintf($text, $member->id));
            });
    }

    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param \Discord\Parts\User\Member $member The member that left the guild.
     *
     * @return void
     */
    public function onGuildMemberRemove(Wrapper $wrapper, PartMember $member): void
    {
        if (Configure::read('debug') == true) {
            return;
        }

        $wrapper->Guild->channels->fetch(Configure::read('Discord.channels.welcome'))
            ->then(function ($channel) use ($member) {
                $channel->sendMessage(sprintf('Fiou, un courant d\'air à emporté **%s**.', $member->username));
            });
    }
}
