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

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

// this page is public and requires no login.

$config = get_config('local_oidcserver');

if (empty($config->enabled)) {
    throw new moodle_exception("Oidc server not enabled in this moodle instance");
}

$jsonconfig = new StdClass();
$jsonconfig->issuer = $CFG->wwwroot;
$jsonconfig->authorization_endpoint = $CFG->wwwroot.'/local/oidcserver/endpoints/authorize.php';
$jsonconfig->token_endpoint = $CFG->wwwroot.'/local/oidcserver/endpoints/token.php';
$jsonconfig->revoke_endpoint = $CFG->wwwroot.'/local/oidcserver/endpoints/revoke.php';
$jsonconfig->logout_endpoint = $CFG->wwwroot.'/local/oidcserver/endpoints/logout.php';
$jsonconfig->userinfo_endpoint = $CFG->wwwroot.'/local/oidcserver/endpoints/userinfo.php';
$jsonconfig->jwks_uri = $CFG->wwwroot.'/local/oidcserver/endpoints/jwks.php';
$jsonconfig->response_types_supported = ['0' => 'code', '1' => 'id_token token'];
$jsonconfig->subject_types_supported = ['0' => 'public'];
$jsonconfig->id_token_signing_alg_values_supported = ['0' => 'RS256'];

header("Content-type:application/json");
echo json_encode($jsonconfig);
die;