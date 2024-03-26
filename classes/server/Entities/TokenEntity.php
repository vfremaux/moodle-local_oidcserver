<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

use DateTimeImmutable;
use StdClass;
use League\OAuth2\Server\Entities\TokenInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

abstract class TokenEntity implements TokenInterface
{

    protected $identifier;

    protected $expirydatetime;

    protected $useridentifier;

    protected $client;

    protected array $scopes;

    public function __construct($id, $identifier, int $time, $useridentifier, ?ClientEntityInterface $client) {
        $this->id = $id;
        $this->identifier = $identifier;
        $di = new DateTimeImmutable();
        $di->setTimestamp($time); 
        $this->expirydatetime = $di;
        $this->useridentifier = $useridentifier;
        $this->client = $client;
        $this->scopes = [];
    }

    abstract public static function getNew($keypathorkey);

    abstract public static function getById($id);

    abstract public static function getByIdentifier($identifier);

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
     * Get the token's expiry date time as Unix time stamp (moodle time style).
     *
     * @return int
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
    public function setExpiryUnixTime(int $time) {
        $di = new DateTimeImmutable();
        $di->setTimestamp($time); 
        $this->expirydatetime = $this->setExpiryDateTime($di);
    }

    /**
     * Set the identifier of the user associated with the token.
     *
     * @param string|int|null $identifier The identifier of the user
     */
    public function setUserIdentifier($identifier) {
        $this->useridentifier = $identifier;
    }

    /**
     * Get the token user's identifier.
     *
     * @return string|int|null
     */
    public function getUserIdentifier() {
        return $this->useridentifier;
    }

    /**
     * Get the client that the token was issued to.
     *
     * @return ClientEntityInterface
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Set the client that the token was issued to.
     *
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client) {
        $this->client = $client;
    }

    /**
     * Associate a scope with the token.
     *
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope) {
        if (!in_array($scope, $this->scopes, /* strict */ true)) {
            $this->scopes[] = $scope;
        }
    }

    /**
     * Return an array of scopes associated with the token.
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes() {
        return $this->scopes;
    }

    /**
     * Commit to DB storage
     * superclasses of TokenEntity will decide in wich table.
     */
    public function commit($table = null) {
        global $DB;

        $record = new StdClass;
        $record->identifier = $this->identifier;
        $record->expirydatetime = $this->getExpiryUnixTime();
        $record->clientidentifier = $this->client->getIdentifier();
        $record->useridentifier = $this->useridentifier;
        $scopes = [];
        foreach ($this->scopes as $scope) {
            $scopes[] = $scope->getIdentifier();
        }
        // Store scopes as a list of string tokens.
        $record->scopes = implode(' ', $scopes);

        if (!is_null($table)) {
            if ($this->id == 0) {
                $this->id = $DB->insert_record($table, $record);
            } else {
                $record->id = $this->id;
                $DB->update_record($table, $record);
            }
        }
        return $record;
    }

    public function generateIdentifier() {
        return md5(uniqid());
    }
}
