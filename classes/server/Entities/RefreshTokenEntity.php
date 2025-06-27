<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/RefreshTokenEntity.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/TokenEntity.php');
require_once($CFG->dirroot.'/local/oidcserver/lib.php');

use StdClass;
use DateTimeImmutable;
use local_oidcserver\OAuth2\Server\Entities\RefreshTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

class RefreshTokenEntity extends TokenEntity implements RefreshTokenEntityInterface
{
    protected $id;

    protected $identifier;

    protected $expirydatetime;

    protected $accesstoken;

    protected function __construct(int $id, string $identifier, int $time, ?AccessTokenEntityInterface $accesstoken = null) {
        $this->id = $id;
        $this->identifier = $identifier;
        $di = new DateTimeImmutable();
        $di->setTimestamp($time); 
        $this->expirydatetime = $di;
        $this->accesstoken = $accesstoken;
    }

    /**
     *
     * @param $keypathorkey not used.
     */
    public static function getNew($keypathorkey) {
        return new RefreshTokenEntity(0, '', 0, null);
    }

    public static function getById($id) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_rtoken', ['id' => $id]);

        $accesstoken = AccessTokenEntity::getByIdentifier($record->accesstokenidentifier);
        return new RefreshTokenEntity($record->id, $record->identifier, $record->exprydatetime, $accesstoken);
    }

    public static function getByIdentifier($identifier) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_rtoken', ['identifier' => $identifier]);

        $accesstoken = AccessTokenEntity::getByIdentifier($record->accesstokenidentifier);
        return new RefreshTokenEntity($record->id, $record->identifier, $record->exprydatetime, $accesstoken);
    }

    public function commit($table = null) {
        global $DB;

        $record = new StdClass;
        $record->identifier = $this->identifier;
        $record->expirydatetime = $this->expirydatetime->getTimestamp();
        $record->accesstokenidentifier = $this->accesstoken->getIdentifier();

        if ($this->id == 0) {
            // try {
                local_oidcserver_debug_trace("Inserting ");
                local_oidcserver_debug_trace($record, LOCAL_OIDCS_TRACE_DATA);
                $this->id = $DB->insert_record('local_oidcserver_rtoken', $record);
                local_oidcserver_debug_trace("Done insert ");
            // } catch (Exception $ex) {
            //    throw new UniqueTokenIdentifierConstraintViolationException();
            //}
        } else {
            $record->id = $this->id;
            local_oidcserver_debug_trace("Updating ");
            $DB->update_record('local_oidcserver_rtoken', $record);
        }
    }

    /**
     * Get the token's identifier.
     *
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Set the token's identifier.
     *
     * @param mixed $identifier
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Get the token's expiry date time.
     *
     * @return DateTimeImmutable
     */
    public function getExpiryDateTime() {
        return $this->expirydatetime;
    }

    /**
     * Get the token's expiry date time.
     *
     * @return DateTimeImmutable
     */
    public function getExpiryUnixTime() {
        return $this->expirydatetime->getTimestamp();
    }

    /**
     * Set the date time when the token expires.
     *
     * @param DateTimeImmutable $dateTime
     */
    public function setExpiryDateTime(DateTimeImmutable $dateTime) {
        $this->expirydatetime = $dateTime;
    }

    /**
     * Set the date time when the token expires.
     *
     * @param int $time
     */
    public function setExpiryUnixTime(int $Time) {
        $di = new DateImmutable();
        $di = $di->setTimestamp($time);
        $this->expirydatetime = $di;
    }

    /**
     * Set the access token that the refresh token was associated with.
     *
     * @param AccessTokenEntityInterface $accessToken
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken) {
        $this->accesstoken = $accessToken;
    }

    /**
     * Get the access token that the refresh token was originally associated with.
     *
     * @return AccessTokenEntityInterface
     */
    public function getAccessToken() {
        return $this->accesstoken;
    }
}
