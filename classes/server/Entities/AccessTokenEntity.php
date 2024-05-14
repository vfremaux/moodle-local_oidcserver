<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/ClientEntity.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/TokenEntity.php');

use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

class AccessTokenEntity extends TokenEntity implements AccessTokenEntityInterface
{
    protected string $keypath;

    protected CryptKey $privatekey;

    protected $revoked;  // Revocation datetime, or 0 if valid token.

    protected function __construct(int $id, string $identifier, $time, $useridentifier, ?ClientEntity $client = null, $keypath = '', $revoked = 0) {
        parent::__construct($id, $identifier, $time, $useridentifier, $client);
        $this->revoked = $revoked;
        if ($keypath instanceof CryptKey) {
            $this->privatekey = $keypath;
            $this->keypath = $keypath->getKeyPath();
        } else {
            $this->keypath = $keypath;
            $this->privatekey = new CryptKey($this->keypath, /* passphrase */ '', /* check file permission */ true);
        }
    }

    public static function getNew($keypathorkey) {
        $newtoken = new AccessTokenEntity(0, '', time(), null, null, $keypathorkey);
        return $newtoken;
    }

    public static function getById($id) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_atoken', ['id' => $id]);

        $client = ClientEntity::getByIdentifier($record->clientidentifier);
        $cryptkey = new CryptKey($record->keypath);
        return new AccessTokenEntity($record->id, $record->identifier, $record->expirydatetime, $record->useridentifier, $client, $cryptkey, $record->revoked);
    }

    public static function getByIdentifier($identifier, $privatekey = null) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_atoken', ['identifier' => $identifier]);

        $client = ClientEntity::getByIdentifier($record->clientidentifier);
        if (is_null($privatekey)) {
            $cryptkey = new CryptKey($record->keypath);
        } else {
            $cryptkey = $privatekey;
        }
        return new AccessTokenEntity($record->id, $record->identifier, $record->expirydatetime, $record->useridentifier, $client, $cryptkey, $record->revoked);
    }

    /**
     * Set a private key used to encrypt the access token.
     */
    public function setPrivateKey(CryptKey $privateKey) {
        $this->keypath = $privateKey->getKeyPath();
    }

    /**
     * Generate a string representation of the access token.
     */
    public function __toString() {
        return $this->getIdentifier();
    }

    public function commit($table = null) {
        global $DB;

        debug_trace("Start commit");
        $record = parent::commit(null);
        $record->keypath = $this->privatekey->getKeyPath();
        $revoked = 0;
        $record->revoked = $revoked;

        if ($this->id == 0) {
            try {
                debug_trace("Insert ");
                debug_trace($record);
                $this->id = $DB->insert_record('local_oidcserver_atoken', $record);
                debug_trace("Done Insert ");
            } catch (Exception $ex) {
                throw new UniqueTokenIdentifierConstraintViolationException();
            }
        } else {
            $record->id = $this->id;
            debug_trace("Update ");
            $DB->update_record('local_oidcserver_atoken', $record);
        }
    }

    public function revoke() {
        $this->revoked = time();
    }

    public function idRevoked() {
        return $this->revoked > 0;
    }
}
