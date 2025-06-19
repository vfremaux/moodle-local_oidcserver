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

// Logout should kill the local IDP session of the user, then send logout request to any other running service for the user.
// We receive token to logout in Authorize header. 
//

// Mount an Authorisation Server and process an Oauth2 AuthnRequest

// Let be sure we have it loaded before session is started, in order to proper deserialize session objects.
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/extlib/oauth_oidc_server/vendor/autoload.php');
require_once($CFG->dirroot.'/local/oidcserver/lib.php');

use local_oidcserver\OAuth2\Server\Repositories\AccessTokenRepository;
use local_oidcserver\OAuth2\Server\Repositories\ClientRepository;

include('../../../config.php');

// Autoload everything (classes, interfaces) needed to operate the Oauth/OpenID server
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/AccessTokenRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/ClientRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/ClientEntity.php');

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

$accessTokenRepository = new AccessTokenRepository($privateKey);
$clientRepository = new ClientRepository();

$headers = getallheaders();

if (!empty($headers['Authorize'])) {

    $bearertoken = clean_param($headers['Authorize'], PARAM_TEXT);
    if (!preg_match('/^Bearer\s+/', $bearertoken)) {
        throw new moodle_exception("Not an oauth Bearer token.");
    }

    $accesstoken = preg_replace('/^Bearer\s+/', '', $bearertoken);

    $atoken = $DB->get_record('local_oidcserver_atoken', ['identifier' => $accesstoken]);
    if ($atoken) {

        if (!empty($config->enablesinglelogout)) {
            $user = $DB->get_record('user', ['username' => $atoken->useridentifier]);
            if (function_exists('debug_trace')) {
                debug_trace("Logging out {$atoken->useridentifier}", TRACE_DEBUG);
            }
            \core\session\manager::kill_user_sessions($user->id);

            // Now get all other living tokens from the user.
            $params = ['useridentifier' => $user->id, 'expirydatetime' => time()];
            $select = " useridentifier = :useridentifier AND expirydatetime > :expirydatetime ";
            $activetokens = $DB->get_records_select('local_oidcserver_atoken', $select, $params);
            foreach ($activetokens as $atoken) {
                // Send logout request to all linked peers.
                $client = $clientrepository->getClientEntity($atoken->clientidentifier);
                $remotelogouturi = $client->getSingleLogoutUri();
                if ($remotelogouturi) {
                    $ch = curl_init($remotelogouturi);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorize: Bearer '.$atoken
                    ]);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                    curl_setopt($ch, CURLOPT_POST, false);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle Oauth2 OidcServer');
                    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
                    // todo : add proxy out.

                    // running services.
                    if (function_exists('debug_trace')) {
                        debug_trace("Sending logout signal with token {$atoken} to $remotelogouturi.", TRACE_DEBUG);
                    }
                    $raw = curl_exec($ch);
                    // Let silently assume that everything is ok.
                }

                $accessTokenRepository->revokeAccessToken($atoken);
            }
        }

        $accessTokenRepository->revokeAccessToken($accesstoken);

    } else {
        if (function_exists('debug_trace')) {
            debug_trace("Oauth Token not found", TRACE_ERRORS);
        }
    }
}


