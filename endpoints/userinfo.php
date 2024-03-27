<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     local_oidcserver
 * @category    local
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Let be sure we have it loaded before session is started, in order to proper deserialize session objects.
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/.extlib/oauth_oidc_server/vendor/autoload.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/.extlib/oauth_oidc_server/vendor/league/oauth2-server/src/RequestTypes/AuthorizationRequest.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/classes/server/Entities/ClientEntity.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/classes/server/Entities/ScopeEntity.php');

// Adhere to Moodle model.
include('../../../config.php');

require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/AccessTokenRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/AccessTokenEntity.php');

use local_oidcserver\OAuth2\Server\Repositories\AccessTokenRepository;
use local_oidcserver\OAuth2\Server\Entities\AccessTokenEntity;
use League\OAuth2\Server\CryptKey;

$config = get_config('local_oidcserver');
if (!$config->enabled) {
    throw new MoodleException("The OIDC/Oauth2 server is disabled y configuration.");
}

if (empty($config->privatekey)) {
    throw new MoodleException("You must provide a valid private key in settings.");
}
if (empty($config->encryptionkey)) {
    throw new MoodleException("You must provide a valid public key in settings.");
}

$privateKey = new CryptKey($config->privatekey, '', true);
$encryptionKey = new CryptKey($config->encryptionkey, '', true);

$headers = getallheaders();
$bearer = $headers['Authorization'];

if (!preg_match('/Bearer\s+([0-9a-zA-Z]+)/', $bearer, $matches)) {
    throw moodle_exeption("Not Oauth bearer");
}

$bearerid = $matches[1];

if (empty($bearerid)) {
    throw moodle_exeption("Empty bearer ID");
}

$accesstoken = AccessTokenEntity::getByIdentifier($bearerid, $privateKey);

$user = $DB->get_record('user', ['username' => $accesstoken->getUserIdentifier()]);
unset($user->password);

$client = $accesstoken->getClient();

// Process to fields filtering
$userprefs = get_user_preferences('oidcconsent_'.$client->get_id(), false, $user);
if ($userprefs) {
    $allowed = ['username', 'email', 'firstname', 'lastname']; // Essential fields for OIDC
    if (preg_match('/passed:\s(.*)/', $userprefs, $matches)) {
        $allowed = $allowed + explode(',', $matches[1]);
    }

    $userarr = get_object_vars($user);
    $sentuserarr = [];
    foreach ($userarr as $key => $value) {
        if (in_array($key, $allowed)) {
            $sentuserarr[$key] = $value;
        }
    }
    $sentuser = (object) $sentuserarr;
} else {
    // Unfiltered unless password.
    $sentuser = $user;
}

echo json_encode($user);