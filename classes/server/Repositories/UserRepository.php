<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Repositories;

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/Repository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/UserEntity.php');

use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\ClientENtityInterface;
use local_oidcserver\OAuth2\Server\Entities\UserEntity;

class UserRepository extends Repository implements UserRepositoryInterface
{

    public function __construct() {
    }

    /* Interface UserRepositoryInterface */

    /**
     * Get a user entity.
     *
     * @param string                $username
     * @param string                $password
     * @param string                $grantType    The grant type used
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        
    }

    public function getUserEntityByIdentifier($identifier) {
        debug_trace("Identifier : ".$identifier);
        return UserEntity::getByIdentifier($identifier);
    }
}
