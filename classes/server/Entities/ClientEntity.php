<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class ClientEntity implements ClientEntityInterface
{
    protected $id;

    protected $identifier;

    protected $name;

    protected $secret;

    protected $redirecturi;

    protected $altredirecturis;

    protected $singlelogouturi;

    protected $isconfidential;

    protected function __construct(int $id, string $identifier, string $name, string $secret, string $redirecturi, 
            string $altredirecturis, string $singlelogouturi, $isconfidential = 0) {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->secret = $secret;
        $this->redirecturi = $redirecturi;
        $this->altredirecturis = $altredirecturis;
        $this->singlelogouturi = $singlelogouturi;
        $this->isconfidential = $isconfidential;
    }

    public static function getNew() {
        return new ClientEntity(0, '', '', '', '', '', 0);
    }

    /**
     *
     */
    public static function getById($id) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_client', ['id' => $id]);
        if (!$record) {
            throw new LocalOIDCServerException();
        }
        return new ClientEntity($record->id, $record->identifier,
            $record->name,
            $record->secret,
            $record->redirecturi,
            $record->altredirecturis,
            $record->singlelogouturi,
            $record->isconfidential);
    }

    /**
     *
     */
    public static function getByIdentifier($identifier) {
        global $DB;

        $record = $DB->get_record('local_oidcserver_client', ['identifier' => $identifier]);
        if (!$record) {
            throw new LocalOIDCServerException();
        }
        return new ClientEntity($record->id, $record->identifier,
            $record->name,
            $record->secret,
            $record->redirecturi ?? '',
            $record->altredirecturis ?? '',
            $record->singlelogouturi ?? '',
            $record->isconfidential);
    }

    public function commit() {
        global $DB;

        $record = new StdClass;
        $record->identifier = $this->identifier;
        $record->name = $this->name;
        $record->secret = $this->srecret;
        $record->redirecturi = $this->redirecturi;
        $record->altredirecturis = $this->altredirecturis;
        $record->singlelogouturi = $this->singlelogouturi;
        $record->isconfidential = $this->isconfidential;

        if ($this->id == 0) {
            $this->id = $DB->insert_record('local_oidcserver_client', $record);
        } else {
            $record->id = $this->id;
            $DB->update_record('local_oidcserver_client', $record);
        }
    }

    /**
     * Ensure it's real unique in our server.
     */
    protected function generateIdentifier() {
        global $DB;

        // Todo self generate a random based unique id.
        $this->identifier = uniqid();
        do {
            $this->identifier = uniqid();
        } while ($DB->record_exists('local_oidcserver_client', ['identifier' => $this->identifier]));
    }

    /**
     * Not used yet.
     */
    protected function generateSecret() {
        // Todo self generate a random based unique id.
        $this->secret = md5(rand(time()));
    }

    /**
     * Validate a secret as being mine.
     */
    public function validateSecret($clientsecret) {
        return $this->secret == $clientsecret;
    }

    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the registered redirect URI (as a string).
     *
     * Alternatively return an indexed array of redirect URIs.
     *
     * @return string|string[]
     */
    public function getRedirectUri() {
        return $this->redirecturi;
    }

    /**
     * Returns the alternative admitted redirect URIs (as an array of strings).
     *
     * Alternatively return an indexed array of redirect URIs.
     *
     * @return string[]
     */
    public function getAltRedirectUris() {
        return preg_split('/[,\s]+/', $this->altredirecturis);
    }

    /**
     * Returns the alternative admitted redirect URIs (as an array of strings).
     * @param string $redirecturi an explicit redirect URI submitted in Auth Request body.
     * @return bool
     */
    public function matchAltRedirectUri(string $redirecturi) : bool {
        $uris = $this->getAltRedirectUris();
       return (in_array(trim($redirecturi)), $uris);
    }

    /**
     * Returns true if the client is confidential.
     *
     * @return bool
     */
    public function isConfidential() {
        return $this->isconfidential;
    }
    
    // Out of interface
    public function get_id() {
        return $this->id;
    }

    /**
     * Returns the registered logout URI (as a string).
     *
     * @return string|string[]
     */
    public function getSingleLogoutUri() {
        return $this->singlelogouturi;
    }
}
