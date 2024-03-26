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

use League\OAuth2\Server\Repositories\RepositoryInterface;

/**
 * Repository base class.
 */
class TokenRepository extends Repository implements RepositoryInterface
{

    protected $privateKey;

    public function __construct($privateKey) {
        $this->privateKey = $privateKey;
    }

}
