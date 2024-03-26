<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Repositories;

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/AuthCodeEntity.php');

use local_oidcserver\OAuth2\Server\Entities\AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

/**
 * Auth code storage interface.
 */
class AuthcodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * Creates a new AuthCode
     *
     * @return AuthCodeEntityInterface
     */
    public function getNewAuthCode() {
        return AuthCodeEntity::getNew(null);
    }

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity) {
        $authCodeEntity->commit();
    }

    /**
     * Revoke an auth code.
     *
     * @param string $codeId
     */
    public function revokeAuthCode($codeId) {
        $authcode = AuthCodeEntity::getByIdentifier($codeId);
        $authcode->revoke();
        $authcode->commit();
    }

    /**
     * Check if the auth code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isAuthCodeRevoked($codeId) {
        $authcode = AuthCodeEntity::getByIdentifier($codeId);

        return $authcode->isRevoked();
    }
}
