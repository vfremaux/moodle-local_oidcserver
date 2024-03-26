<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Repositories;

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/TokenRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/AccessTokenEntity.php');

use Exception;
use local_oidcserver\OAuth2\Server\Entities\AccessTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

/**
 * Access token interface.
 */
class AccessTokenRepository extends TokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * Create a new access token
     *
     * @param ClientEntityInterface  $clientEntity
     * @param ScopeEntityInterface[] $scopes
     * @param mixed                  $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null) {

        if (is_null($clientEntity)) {
            throw new Exception("Null client reference when generating access token");
        }

        if (is_null($userIdentifier)) {
            throw new Exception("Null user reference when generating access token");
        }

        $newtoken = AccessTokenEntity::getNew($this->privateKey);
        foreach ($scopes as $scope) {
            $newtoken->addScope($scope);
        }

        $newtoken->generateIdentifier();
        $newtoken->setUserIdentifier($userIdentifier);
        $newtoken->setClient($clientEntity);

        return $newtoken;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) {
        $accessTokenEntity->commit();
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId) {
        $token = AccessTokenEntity::getByIdentifier($tokenId);
        $token->revoke();
        $token->commit();
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId) {
        $token = AccessTokenEntity::getByIdentifier($tokenId);
        return $token->isRevoked();
    }
}
