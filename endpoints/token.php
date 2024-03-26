<?php
// Mount an Authorisation Server and process an Oauth2 Token request

// Adhere to Moodle model.
include('../../../config.php');
// Autoload everything (classes, interfaces) needed to operate the Oauth/OpenID server
require_once($CFG->dirroot.'/local/oidcserver/.extlib/oauth_oidc_server/vendor/autoload.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/ClientRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/AuthCodeRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/AccessTokenRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/RefreshTokenRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/IdentityRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/ScopeRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Repositories/UserRepository.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Http/ServerRequest.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Http/Stream.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/server/Http/Response.php');

use OpenIDConnectServer\IdTokenResponse;
use OpenIDConnectServer\ClaimExtractor;
use local_oidcserver\OAuth2\Server\Repositories\ClientRepository;
use local_oidcserver\OAuth2\Server\Repositories\AccessTokenRepository;
use local_oidcserver\OAuth2\Server\Repositories\AuthCodeRepository;
use local_oidcserver\OAuth2\Server\Repositories\RefreshTokenRepository;
use local_oidcserver\OAuth2\Server\Repositories\ScopeRepository;
use local_oidcserver\OpenID\Server\Repositories\IdentityRepository;
use local_oidcserver\Oauth2\Server\Repositories\UserRepository;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\CryptKey;
use local_oidcserver\psr\http\ServerRequest; // Implementation of RequestInterface
use local_oidcserver\psr\http\Stream; // Implementation of StreamInterface
use local_oidcserver\psr\http\Response;

// build essential objects needed to build server instance.

$config = get_config('local_oidcserver');

if (!$config->enabled) {
    throw new MoodleException("The OIDC/Oauth2 server is disabled y configuration.");
}

debug_trace($_REQUEST);

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

    // Try to respond to the request
    debug_trace("Server tries to respond to token request ");
    $redirectresponse = $server->respondToAccessTokenRequest($request, $response);
    debug_trace("Got response... sending ");
    $redirectresponse->send();
    debug_trace("Got response... sent ");
    die;

} catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {

    debug_trace("Token request failed on exception ");
    // All instances of OAuthServerException can be formatted into a HTTP response
    $str = $exception->generateHttpResponse($response);
    debug_trace($str);
    $response->send();
} catch (\Exception $exception) {

    // Unknown exception
    $body = new Stream(fopen('php://temp', 'r+'));
    $body->write($exception->getMessage());
    $response = $response->withStatus(500)->withBody($body);
    echo $response;
}