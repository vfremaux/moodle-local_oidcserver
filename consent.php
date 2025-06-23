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

// Let be sure we have it loaded before session is started, in order to proper deserialize session objects.
require_once(dirname(dirname(dirname(__FILE__))).'/local/oidcserver/extlib/oauth_oidc_server/vendor/autoload.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/oidcserver/extlib/oauth_oidc_server/vendor/league/oauth2-server/src/RequestTypes/AuthorizationRequest.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/oidcserver/classes/server/Entities/ClientEntity.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/oidcserver/classes/server/Entities/ScopeEntity.php');

// Adhere to Moodle.
require('../../config.php');
require($CFG->dirroot.'/local/oidcserver/forms/consent_form.php');

$url = new moodle_url('/local/oidcserver/consent.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('embedded');

require_login();

if (!isset($SESSION->authRequest)) {
    echo "No authorisation request pending in session. ";
    die;
}
$authRequest = $SESSION->authRequest;

$mform = new consent_form($url, ['scopes' => $authRequest->getScopes()]);

if ($mform->is_cancelled()) {
    $redirecturi = $authRequest->getRedirectUri();
    redirect($redirecturi);
}

if ($data = $mform->get_data()) {
    try {
        // mark consent preference for this client id.
        $client = $authRequest->getclient();
        $fields = preg_grep('/^allow_/', array_keys(get_object_vars($data)));
        $allowed = [];
        foreach ($fields as $profileallowance) {
            if (!empty($data->$profileallowance)) {
                $allowed[] = str_replace('allow_', '', $profileallowance);
            }
        }
        set_user_preference('oidcconsent_'.$client->get_id(), 'passed: '.implode(',', $allowed), $USER);

        $redirecturl = new moodle_url('/local/oidcserver/endpoints/authorize.php');
        redirect($redirecturl);
    } catch (Exception $ex) {
        redirect(get_login_url());
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('consenthead', 'local_oidcserver'));
$mform->display();
echo $OUTPUT->footer();