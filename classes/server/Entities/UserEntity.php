<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;
use OpenIDConnectServer\Entities\ClaimSetInterface;

class UserEntity implements UserEntityInterface, ClaimSetInterface
{

    protected $user;

    protected $attributes;

    public function __construct($user) {
        $this->user = $user;
        $this->attributes = (array) $user; // First trivial step, but we might add custom fields values there.
    }

    public static function getByIdentifier($identifier) {
        global $DB;

        $user = $DB->get_record('user', ['username' => $identifier]);

        $userentity = new UserEntity($user);
        debug_trace("User entity:");
        debug_trace($userentity);
        return $userentity;
    }

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier() {

        // Will depend on client config, probably. 

        return $this->user->username;
    }

    public function getClaims() {
        return $this->attributes;
    }
}
