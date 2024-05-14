<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace local_oidcserver\OAuth2\Server\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;
use OpenIDConnectServer\Entities\ClaimSetInterface;
use moodle_url;

class UserEntity implements UserEntityInterface, ClaimSetInterface
{

    protected $user;

    protected $client;

    protected $attributes;

    public function __construct($user) {
        $this->user = $user;
        $this->attributes = (array) $user; // First trivial step, but we might add custom fields values there.
    }

    public function setClient(ClientEntity $client) {
        $this->client = $client;
    }

    public static function getByIdentifier($identifier) {
        global $DB;

        $user = $DB->get_record('user', ['username' => $identifier]);

        $userentity = new UserEntity($user);
        return $userentity;
    }

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier() {

        // Will depend on client config, probably. 

        return $this->user->username;
    }

    public function getClaims() {
        global $CFG;

        // Map standard openid claims on moodle attributes.
        // TODO : invalidate claim data if user preference do not allow transmission in the client context.

        $config = get_config('local_oidcserver');

        $checkallowance = false;
        $allowancearr = [];
        if (!empty($config->getconsent) && !is_null($this->client)) {
            $checkallowance = true;
            $allowance = get_user_preferences('oidcconsent_'.$this->client->get_id(), '', 'passed: '.implode(',', $allowed), $this->user);
            if (!empty($allowance)) {
                $allowancearr = explode(',', str_replace('passed: ', '', $allowance));
            }
        }

        $this->attributes['name'] = $this->attributes['firstname'].' '.$this->attributes['lastname'];
        $this->attributes['given_name'] = $this->attributes['firstname'];
        $this->attributes['family_name'] = $this->attributes['lastname'];
        if (!$checkallowance || in_array('middlename', $allowancearr)) {
            $this->attributes['middle_name'] = $this->attributes['middlename'];
        }
        if (!$checkallowance || in_array('alternatename', $allowancearr)) {
            $this->attributes['nickname'] = $this->attributes['alternatename'];
        }
        if (!$checkallowance || in_array('phone1', $allowancearr)) {
            $this->attributes['phone_number'] = $this->attributes['phone1'];
            $this->attributes['phone_number_verified'] = $this->attributes['phone1'];
        }

        $this->attributes['preferred_username'] = $this->attributes['username'];
        $this->attributes['profile'] = new moodle_url($CFG->wwwroot.'/user/profile.php', ['id' => $this->attributes['id']]);
        $this->attributes['zoneinfo'] = $this->attributes['timezone'];
        $this->attributes['locale'] = $this->attributes['lang'];
        $this->attributes['updated_at'] = $this->attributes['timemodified'];

        if ($checkallowance && !in_array('address', $allowancearr)) {
            unset($this->attributes['address']);
        }

        if ($checkallowance && !in_array('picture', $allowancearr)) {
            unset($this->attributes['picture']);
        }

        return $this->attributes;
    }
}
