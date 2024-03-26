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
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/ClientEntity.php');

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use local_oidcserver\OAuth2\Server\Entities\ClientEntity;

/**
 * Client storage interface.
 */
class ClientRepository extends Repository implements ClientRepositoryInterface
{
    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     *
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier) {
        return ClientEntity::getByIdentifier($clientIdentifier);
    }

    /**
     * Validate a client's secret.
     *
     * @param string      $clientIdentifier The client's identifier
     * @param null|string $clientSecret     The client's secret (if sent)
     * @param null|string $grantType        The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType) {
        try {
            $client = ClientEntity::getByIdentifier($clientIdentifier);
            return $client->validateSecret($clientSecret);
        } catch(Exception $ex) {
            return false;
        }
    }
}
