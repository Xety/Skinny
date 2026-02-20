<?php
namespace Skinny\Network;

use Discord\Discord;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Channel\Message;
use Discord\Repository\Guild\MemberRepository;
use Skinny\Api\Api;
use Skinny\Core\Configure;
use Skinny\Module\ModuleManager;
use Skinny\Singleton\Singleton;

/**
 * This class is a wrapper to separate all the Discord classes into variables
 * for a better accessibility and clarity when developing modules.
 */
class Wrapper extends Singleton
{
    /**
     * The API instance.
     *
     * @var \Skinny\Api\Api
     */
    public Api $API;

    /**
     * The ModuleManager instance.
     *
     * @var \Skinny\Module\ModuleManager
     */
    public ModuleManager $ModuleManager;

    /**
     * The Message instance.
     *
     * @var \Discord\Parts\Channel\Message|null
     */
    public ?Message $Message = null;

    /**
     * The Guild instance.
     *
     * @var \Discord\Parts\Guild\Guild|null
     */
    public ?Guild $Guild = null;

    /**
     * The Members instance.
     *
     * @var \Discord\Repository\Guild\MemberRepository|null
     */
    public ?MemberRepository $Members = null;

    /**
     * The Client instance.
     *
     * @var \Discord\Discord
     */
    public Discord $Discord;

    /**
     * Set the instances to the Wrapper.
     *
     * @param \Skinny\Module\ModuleManager $moduleManager The ModuleManager object.
     * @param \Discord\Discord $discord The client object.
     * @param \Discord\Parts\Channel\Message|null $message The messages object.
     *
     * @return self Return this Wrapper.
     */
    public function setInstances(ModuleManager $moduleManager, Discord $discord, ?Message $message = null): self
    {
        $this->API = new Api();

        $this->ModuleManager = $moduleManager;
        $this->Message = $message;
        $this->Discord = $discord;

        // Fetch guild synchronously from cache if available
        $guildId = Configure::read('Discord.guild');
        $this->Guild = $discord->guilds->get('id', $guildId);

        if ($this->Guild) {
            $this->Members = $this->Guild->members;
        }

        return $this;
    }

    /**
     * Fetch guild asynchronously if not in cache.
     *
     * @return \React\Promise\PromiseInterface
     */
    public function fetchGuild(): \React\Promise\PromiseInterface
    {
        $guildId = Configure::read('Discord.guild');
        return $this->Discord->guilds->fetch($guildId)->then(function (Guild $guild) {
            $this->Guild = $guild;
            $this->Members = $guild->members;
            return $guild;
        });
    }
}
