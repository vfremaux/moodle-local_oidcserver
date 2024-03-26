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
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/ScopeEntity.php');

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use local_oidcserver\OAuth2\Server\Entities\ScopeEntity;

/**
 * Scope interface.
 */
class ScopeRepository extends Repository implements ScopeRepositoryInterface
{
    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface|null
     */
    public function getScopeEntityByIdentifier($identifier) {
        return ScopeEntity::getByIdentifier($identifier);
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string                 $grantType
     * @param ClientEntityInterface  $clientEntity
     * @param null|string            $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        // Todo : 
        // Create mapping table client <-> scope and get the mapping pairs to validate
        // 
        // In the meanwhile, neutral passall
        return $scopes;
    }
}
