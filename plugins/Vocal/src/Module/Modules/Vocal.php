<?php
namespace Vocal\Module\Modules;

use Discord\Parts\WebSockets\VoiceStateUpdate;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;

class Vocal implements ModuleInterface
{
    private $member;

    private array $temporaryChannels = [];

    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param \Discord\Parts\WebSockets\VoiceStateUpdate $state The voice state update.
     * @param \Discord\Parts\WebSockets\VoiceStateUpdate|null $oldState The old voice state.
     *
     * @return void
     */
    public function onVoiceStateUpdate(Wrapper $wrapper, VoiceStateUpdate $state, ?VoiceStateUpdate $oldState = null): void
    {
        // Prevent some errors
        if (is_null($state->channel_id)) {
            return;
        }

        $wrapper->Guild->channels->fetch($state->channel_id)->then(function ($channel) use ($wrapper, $state) {
            if (is_null($channel) ||
                // Vocal Tribus | Jeux Divers
                !in_array($channel->parent_id, [634190444326158338, 556167652754718747]) ||
                // CLIQUEZ-ICI Vocal Tribus
                $channel->id == 634190445509083176 ||
                // CLIQUEZ-ICI Jeux Divers
                $channel->id == 651986759021887509) {
                return;
            }

            // Prevent for setting the permission for every @Membres that join the channel. Only the owner.
            if ($channel->members->count() != 1) {
                return;
            }

            // Prevent some errors
            if (is_null($state->user_id)) {
                return;
            }

            // Get the member
            $wrapper->Members->fetch($state->user_id)->then(function ($member) use ($wrapper, $channel) {
                $hasRole = $member->roles->has(386617500516876289);

                // If the user has not the role @Membres, dont set the permission.
                if ($hasRole == false) {
                    return;
                }

                // Set the permission for the user for this channel.
                $channel->setPermissions($member, [
                    'manage_channels',
                    'stream',
                    'view_channel',
                    'connect',
                    'speak'
                ]);
            });
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param mixed $channel The new created voice channel.
     *
     * @return void
     */
    public function onChannelCreate(Wrapper $wrapper, $channel): void
    {
        // Disabled for now
    }
}
