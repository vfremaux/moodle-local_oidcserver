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
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/RefreshTokenEntity.php');

use local_oidcserver\OAuth2\Server\Entities\RefreshTokenEntity;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\AccessTokenRepositoryInterface;

/**
 * Access token interface.
 */
class RefreshTokenRepository extends TokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * Create a new refresh token
     *
     * @return RefreshTokenEntityInterface
     */
    public function getNewRefreshToken() {

        $newtoken = RefreshTokenEntity::getNew($this->privateKey);
        $newtoken->generateIdentifier();

        return $newtoken;
    }

    /**
     * Persists a new refresh token to permanent storage.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity) {
        $refreshTokenEntity->commit();
    }

    /**
     * Revoke a refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId) {
        $token = RefreshTokenEntity::getByIdentifier($tokenId);
        $token->revoke();
        $token->commit();

        // revoke accesstoken associated to it.
        $accesstoken = $token->accesstoken;
        $accesstoken->revoke();
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId) {
        $token = RefreshTokenEntity::getByIdentifier($tokenId);
        return $token->isRevoked();
    }
}
