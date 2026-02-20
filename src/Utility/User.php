<?php
declare(strict_types=1);

namespace Skinny\Utility;

use Skinny\Core\Configure;
use Skinny\Network\Wrapper;

class User
{
    /**
     * Checks if the given user has permission to perform an action.
     *
     * @param \Skinny\Network\Wrapper $wrapper The wrapper instance.
     * @param array $authorized Authorized users/roles for the permissions.
     *
     * @return bool Whether the user has the permission or not.
     */
    public static function hasPermission(Wrapper $wrapper, array $authorized = []): bool
    {
        // Check if the user id is a bot
        if ($wrapper->Message->author->bot == true) {
            return true;
        }

        // Check the user id
        if (in_array($wrapper->Message->author->id, $authorized)) {
            return true;
        }

        // Get member roles - member can be null in DMs
        $member = $wrapper->Message->member;
        if ($member === null || $member->roles === null) {
            return false;
        }

        $roles = array_keys($member->roles->toArray());

        foreach ($roles as $id => $role) {
            if (in_array($role, $authorized)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the highest role by his position from a roles list.
     *
     * @param array $roles The roles list.
     *
     * @return int The role ID.
     */
    public static function getHighestRole(array $roles): int
    {
        foreach ($roles as $index => $obj) {
            $numbers[$index] = $obj->position;
        }

        // To avoid error when the user has no role.
        if (is_null($numbers)) {
            return 0;
        }

        return array_keys($numbers, max($numbers))[0];
    }
}
