<?php

// Mount an Authorisation Server and process an Oauth2 AuthnRequest

// Let be sure we have it loaded before session is started, in order to proper deserialize session objects.
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/local/oidcserver/.extlib/oauth_oidc_server/vendor/autoload.php');

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


