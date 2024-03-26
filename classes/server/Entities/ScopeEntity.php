<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

class ScopeEntity implements ScopeEntityInterface
{

    protected $id;

    // Minimal data set for ScopeEntityInterface
    protected $identifier;

    protected $description;

    protected function __construct(int $id, string $identifier, string $description) {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->description = $description;
    }

    public static function getNew() {
        return new ScopeEntity(0, '', '');
    }

    public static function getById($id) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_scope', ['id' => $id]);
        if (!$record) {
            throw new OAuthServerException("Undefined scope by id $id ");
        }
        return new ScopeEntity($record->id, $record->identifier, $record->description);
    }

    public static function getByIdentifier($identifier) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_scope', ['identifier' => $identifier]);
        if (!$record) {
            throw new OAuthServerException("Undefined scope by Identifier $identifier ", 500, "Bad confoguration", 500);
        }
        return new ScopeEntity($record->id, $record->identifier, $record->description);
    }

    public function commit() {
        global $DB;

        $record = new StdClass;
        $record->identifier = $this->identifier;
        $record->description = $this->descripion;

        if ($this->id == 0) {
            $this->id = $DB->insert_record('local_oidcserver_scope', $record);
        } else {
            $record->id = $this->id;
            $DB->update_record('local_oidcserver_scope', $record);
        }
    }

    /**
     * Get the scope's identifier.
     *
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /** JSONSerializable **/

    public function jsonSerialize() {
        return json_encode($this);
    }

    // Out of Oauth interface / Moodle internal interface.
    public function get_id() {
        return $this->id;
    }

    public function get_description() {
        return $this->description;
    }
}
