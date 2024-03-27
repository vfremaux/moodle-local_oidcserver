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
 * Version details.
 *
 * @package    local_oidcserver
 * @category   local
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  2010 onwards Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

// Privacy.
$string['privacy:metadata'] = 'The OIDC Server Local plugin does not store any personal data about any user.';

$string['addclient'] = 'Register new client';
$string['addscope'] = 'Add scope';
$string['allowdeny'] = 'Allow / Deny';
$string['allowdenyorder'] = 'Allow/Deny order';
$string['authcodes'] = 'Auth codes';
$string['clientname'] = 'Name';
$string['clients'] = 'Clients';
$string['configenabled'] = 'Enable';
$string['configenabled_desc'] = 'Enable response of endpoints';
$string['configencryptionkey'] = 'Encryption key (public key)';
$string['configencryptionkey_desc'] = 'The public counter part of our key so that clients can encrypt messages for us, or decode our messages.';
$string['configencryptionalgorithm'] = 'Encryption algorithm of the keys';
$string['configencryptionalgorithm_desc'] = 'RSA or HMAC are supported.';
$string['configfeatures'] = 'OIDC Server configuration';
$string['configgetconsent'] = 'Ask for user consent';
$string['configgetconsent_desc'] = 'If enabled, an additional step in login process will ask for data transmission consent once.';
$string['configprivatekey'] = 'Private key';
$string['configprivatekey_desc'] = 'Our own private and secret key. (Give to no one else!)';
$string['consenthead'] = 'Personal data exchange with remote service';
$string['consenthelp'] = 'By consenting to authentication, you will transmit following personal information to the calling service: ';
$string['consenthelptail'] = 'You may uncheck some data to send, resulting in your remote profile baing incomplete. If you decline the whole transmission set, your remote profile cannot be created..';
$string['denyallow'] = 'Deny / Allow';
$string['description'] = 'Description';
$string['editclient'] = 'Edit client';
$string['editscope'] = 'Edit scope';
$string['generate'] = ' Generate: ';
$string['iconsent'] = 'I consent';
$string['identifier'] = 'Identifier';
$string['isconfidential'] = 'Is confidential';
$string['manageoidcserver'] = 'Manage OIDC server entities';
$string['oidcadmin'] = 'Oidc Server Administration';
$string['pluginname'] = 'IODC/Oauth server';
$string['redirecturi'] = 'Redirect uri';
$string['redirecturi_help'] = 'The URI the user will be redirected to after all the handshakes and authentication has been done.';
$string['scopes'] = 'Scopes';
$string['secret'] = 'Secret';
$string['singlelogouturi'] = 'single logout URI';
$string['singlelogouturi_help'] = 'If provisionned, the Url of an endpoint capable to logout the remote user session in SP peer.';
$string['userallow'] = 'Allow users';
$string['userdeny'] = 'Deny users';

$string['userallow_help'] = "
Filters the local users allowed to be authorized for this client. One expression per line. Passes if at least one rule is matched. Pass all if empty.\n
If the rule is a single rexexp (no prefix, or REGEXP: prefix), will apply to username.\n
If the rule has MOODLESCRIPT: prefix, will be evaluated by a moodlescript engine, in the system context.
";
$string['userdeny_help'] = "
Filters the local users allowed to be authorized for this client. One expression per line. Blocks if at least one rule is matched. Blocks none if empty.\n
If the rule is a single rexexp (no prefix, or REGEXP: prefix), will apply to username.\n
If the rule has MOODLESCRIPT: prefix, will be evaluated by a moodlescript engine, in the system context.
";

include(__DIR__.'/pro_additional_strings.php');