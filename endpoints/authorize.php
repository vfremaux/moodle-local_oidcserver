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
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Mount an Authorisation Server and process an Oauth2 AuthnRequest

// Let be sure we have it loaded before session is started, in order to proper deserialize session objects.
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/extlib/oauth_oidc_server/vendor/autoload.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/extlib/oauth_oidc_server/vendor/league/oauth2-server/src/RequestTypes/AuthorizationRequest.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/classes/server/Entities/ClientEntity.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/classes/server/Entities/ScopeEntity.php');


// Adhere to Moodle model.
include('../../../config.php');
// Autoload everything (classes, interfaces) needed to operate the Oauth/OpenID server
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/ClientRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/AuthCodeRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/AccessTokenRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/RefreshTokenRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/IdentityRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/ScopeRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/UserRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Entities/UserEntity.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Http/ServerRequest.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Http/Stream.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Http/Response.php');
require_once($CFG->dirroot.'/local/oidcserver/lib.php');

use OpenIDConnectServer\IdTokenResponse;
use OpenIDConnectServer\ClaimExtractor;
use local_oidcserver\OAuth2\Server\Repositories\ClientRepository;
use local_oidcserver\OAuth2\Server\Repositories\AccessTokenRepository;
use local_oidcserver\OAuth2\Server\Repositories\AuthCodeRepository;
use local_oidcserver\OAuth2\Server\Repositories\RefreshTokenRepository;
use local_oidcserver\OAuth2\Server\Repositories\ScopeRepository;
use local_oidcserver\OpenID\Server\Repositories\IdentityRepository;
use local_oidcserver\Oauth2\Server\Repositories\UserRepository;
use local_oidcserver\Oauth2\Server\Entities\UserEntity;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OauthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\CryptKey;
use local_oidcserver\psr\http\ServerRequest; // Implementation of RequestInterface
use local_oidcserver\psr\http\Stream; // Implementation of StreamInterface
use local_oidcserver\psr\http\Response;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

// build essential objects needed to build server instance.

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

$clientRepository = new ClientRepository();
$scopeRepository = new ScopeRepository();
$accessTokenRepository = new AccessTokenRepository($privateKey);
$authCodeRepository     = new AuthCodeRepository($privateKey);
$refreshTokenRepository = new RefreshTokenRepository($privateKey);

$userRepo = new UserRepository();
$responseType = new IdTokenResponse(new IdentityRepository($userRepo), new ClaimExtractor());

$server = new AuthorizationServer(
    $clientRepository,
    $accessTokenRepository,
    $scopeRepository,
    $privateKey,
    $encryptionKey,
    $responseType
);

$grant = new AuthCodeGrant(
    $authCodeRepository,
    $refreshTokenRepository,
    new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
);

$grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
$grant->setEncryptionKey($encryptionKey);
$grant->setPrivateKey($privateKey);

// Enable the authentication code grant on the server
$server->enableGrantType(
    $grant,
    new \DateInterval('PT1H') // access tokens will expire after 1 hour
);

// To do : Build a PSR7 COmpatible Request with moodle middleframe
$request = ServerRequest::createFromGlobals();
$response = new Response(200);

try {

    if (!isloggedin()) {

        $authRequest = $server->validateAuthorizationRequest($request);

        $SESSION->authRequest = $authRequest;
        $autorizeurl = new moodle_url('/local/oidcserver/endpoints/authorize.php', ['prompt' => optional_param('prompt', '', PARAM_TEXT)]);
        $SESSION->wantsurl = $autorizeurl->out();

        // Let start the moodleside login sequence

        redirect(get_login_url());

    } else {

        // echo "was logged in ";

        if (isset($SESSION->authRequest)) {
            $authRequest = $SESSION->authRequest;

            // At this point you should make some controls about user, if this class of users are allowed
            // to be authorized.

            $client = $authRequest->getclient();

            if (local_oidcserver_supports_feature('extended/userfiltering')) {
                include_once($CFG->dirroot.'/local/oidcserver/pro/localprolib.php');
                \local_oidcserver\local_pro_manager::check_user($client);
            }

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Requires a bit more than a ClientEntityInterface to get moodle id (shorter then client identifier)
            $hasmarked = get_user_preferences('oidcconsent_'.$client->get_id(), false, $USER);
            $forcedconsent = optional_param('prompt', false, PARAM_TEXT);
            if ((!$hasmarked && !empty($config->getconsent) && is_null($authRequest->getUser())) || ($forcedconsent == 'consent')) {
                $consenturl = new moodle_url('/local/oidcserver/consent.php');
                redirect($consenturl);
            }

            // At this point you should redirect the user to an authentication page.
            // This form will ask the user to approve the client and the scopes requested.
            $oidcuser = new UserEntity($USER);
            $oidcuser->setClient($client); // Do allow consent checking against this user/client.
            $authRequest->setUser($oidcuser);

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            // echo "Pre conmplete ";
            $redirectresponse = $server->completeAuthorizationRequest($authRequest, $response);

            $redirectresponse->send();
            unset($SESSION->authRequest);
            die;
        } else {
            // echo "No authorisation request pending in session. ";
            // Process new authrequest agreeing directly as already in session.
            $authRequest = $server->validateAuthorizationRequest($request);
            $SESSION->authRequest = $authRequest;

            $client = $authRequest->getclient();

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Requires a bit more than a ClientEntityInterface to get moodle id (shorter then client identifier)
            $hasmarked = get_user_preferences('oidcconsent_'.$client->get_id(), false, $USER);
            $forcedconsent = optional_param('prompt', false, PARAM_TEXT);
            if ((!$hasmarked && !empty($config->getconsent) && is_null($authRequest->getUser())) || $forcedconsent) {
                $consenturl = new moodle_url('/local/oidcserver/consent.php');
                redirect($consenturl);
            }

            // Do NOT put in session an we are procesisng immediately.
            $oidcuser = new UserEntity($USER);
            $oidcuser->setClient($client); // Do allow consent checking against this user/client.
            $authRequest->setUser($oidcuser);

            // Immediately approve.
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            $redirectresponse = $server->completeAuthorizationRequest($authRequest, $response);
            unset($SESSION->authRequest);
            $redirectresponse->send();
            die;
        }

    }

} catch (OAuthServerException $exception) {

    // All instances of OAuthServerException can be formatted into a HTTP response
    echo $exception->generateHttpResponse($response);

} catch (\Exception $exception) {

    // Unknown exception
    $body = new Stream(fopen('php://temp', 'r+'));
    $body->write("Unhandled Exception ".get_class($exception)." : ".$exception->getMessage());
    $response = $response->withStatus(500)->withBody($body);
    echo $response;
    die;
}