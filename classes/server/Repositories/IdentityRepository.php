<?php
/**
 * @author Steve Rhoades <sedonami@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace local_oidcserver\OpenID\Server\Repositories;

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/UserEntity.php');

use OpenIDConnectServer\Repositories\IdentityProviderInterface;
use local_oidcserver\OAuth2\Server\Entities\UserEntity;
use local_oidcserver\OAuth2\Server\Repositories\UserRepository;

class IdentityRepository implements IdentityProviderInterface
{

    protected $userRepo;

    public function __construct(UserRepository $userRepo) {
        $this->userRepo = $userRepo;
    }


    public function getUserEntityByIdentifier($identifier) : UserEntity {
        return $this->userRepo->getUserEntityByIdentifier($identifier);
    }
}
