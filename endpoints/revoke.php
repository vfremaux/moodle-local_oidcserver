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

// Mount an Authorisation Server and process an Oauth2 AuthnRequest

// Let be sure we have it loaded before session is started, in order to proper deserialize session objects.
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/extlib/oauth_oidc_server/vendor/autoload.php');

use local_oidcserver\OAuth2\Server\Repositories\AccessTokenRepository;
use League\OAuth2\Server\CryptKey;

include('../../../config.php');
// Autoload everything (classes, interfaces) needed to operate the Oauth/OpenID server
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/AccessTokenRepository.php');

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

// We suppose only the bearer known its access token. So its legimitate to process such revoke.

$accessTokenRepository = new AccessTokenRepository($privateKey);

$headers = getallheaders();

if (!empty($headers['Authorize'])) {

    $bearertoken = clean_param($headers['Authorize'], PARAM_TEXT);
    if (!preg_match('/^Bearer\s+/', $bearertoken)) {
        throw new moodle_exception("Not an oauth Bearer token.");
    }

    $accesstoken = preg_replace('/^Bearer\s+/', '', $bearertoken);
    $accessTokenRepository->revokeAccessToken($accesstoken);
}


