<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/TokenEntity.php');

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

class AuthCodeEntity extends TokenEntity implements AuthCodeEntityInterface
{
    protected $redirecturi;

    protected function __construct($id, string $identifier, $time, $useridentifier, ?ClientEntityInterface $client = null, string $redirecturi, int $revoked) {
        parent::__construct($id, $identifier, $time, $useridentifier, $client);
        $this->redirecturi = $redirecturi;
        $this->revoked = $revoked;
    }

    public static function getNew($keypathorkey) {
        return new AUthCodeEntity(0, '', 0, '', null, '', 0);
    }

    public static function getById($id) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_authcode', ['id' => $id]);

        $client = ClientEntity::getByIdentifier($record->clientidentifier);
        return new AuthCodeEntity($record->id, $record->identifier, $record->expirydatetime ?? 0, $record->useridentifier, $client, $record->redirecturi, $record->revoked);
    }

    public static function getByIdentifier($identifier) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_authcode', ['identifier' => $identifier]);

        $client = ClientEntity::getByIdentifier($record->clientidentifier);
        return new AuthCodeEntity($record->id, $record->identifier, $record->expirydatetime ?? 0, $record->useridentifier, $client, $record->redirecturi, $record->revoked);
    }

    /**
     * Set the redirect Ur of this auth code.
     */
    public function setRedirectUri($redirectUri) {
        $this->redirecturi = $redirectUri;
    }

    /**
     * Get the redirectUri.
     */
    public function getRedirectUri() {
        return $this->redirecturi;
    }

    /**
     * Generate a string representation of the access token.
     */
    public function __toString() {
        return serialize(this);
    }

    public function commit($table = null) {
        global $DB;

        $record = parent::commit(null);
        $record->redirecturi = $this->redirecturi;
        $record->revoked = $this->revoked;

        if ($this->id == 0) {
            try {
                $this->id = $DB->insert_record('local_oidcserver_authcode', $record);
            } catch (Exception $ex) {
                throw new UniqueTokenIdentifierConstraintViolationException();
            }
        } else {
            $record->id = $this->id;
            $DB->update_record('local_oidcserver_authcode', $record);
        }
    }

    public function revoke() {
        $this->revoked = time();
    }

    public function isRevoked() {
        return $this->revoked > 0;
    }
}
