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
 * This file contains functions used by the examtraining report
 *
 * @package    local    
 * @subpackage oidcserver
 * @copyright  2012 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/local/oidcserver/lib.php');

if ($hassiteconfig) {

    // Needs this condition or there is error on login page.

    $settings = new admin_settingpage('localsettingoidcserver', get_string('pluginname', 'local_oidcserver'));
    $ADMIN->add('localplugins', $settings);

    $label = get_string('configfeatures', 'local_oidcserver');
    $settings->add(new admin_setting_heading('featureshdr', $label, ''));

    $key = 'local_oidcserver/enabled';
    $label = get_string('configenabled', 'local_oidcserver');
    $desc = '';
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

    $algoptions = [
        'RSA' => 'RSA (RS256)',
        'HMAC'=> 'HMAC (HS256)'
    ];
    $key = 'local_oidcserver/encryptionalgorithm';
    $label = get_string('configencryptionalgorithm', 'local_oidcserver');
    $desc = get_string('configencryptionalgorithm_desc', 'local_oidcserver');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'RSA', $algoptions));

    $key = 'local_oidcserver/privatekey';
    $label = get_string('configprivatekey', 'local_oidcserver');
    $desc = get_string('configprivatekey_desc', 'local_oidcserver');
    $settings->add(new admin_setting_configtextarea($key, $label, $desc, ''));

    $key = 'local_oidcserver/encryptionkey';
    $label = get_string('configencryptionkey', 'local_oidcserver');
    $desc = get_string('configencryptionkey_desc', 'local_oidcserver');
    $settings->add(new admin_setting_configtextarea($key, $label, $desc, ''));

    $key = 'local_oidcserver/clientskeysize';
    $label = get_string('configclientskeysize', 'local_oidcserver');
    $desc = get_string('configclientskeysize_desc', 'local_oidcserver');
    $default = 13;
    $settings->add(new admin_setting_configtextarea($key, $label, $desc, $default));

    $key = 'local_oidcserver/getconsent';
    $label = get_string('configgetconsent', 'local_oidcserver');
    $desc = get_string('configgetconsent_desc', 'local_oidcserver');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, true));

    $key = 'local_oidcserver/forceopeningcors';
    $label = get_string('configforceopeningcors', 'local_oidcserver');
    $desc = get_string('configforceopeningcors_desc', 'local_oidcserver');
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, false));

    $manageurl = new moodle_url('/local/oidcserver/index.php');
    $label = get_string('manageoidcserver', 'local_oidcserver');
    $adminexternal = new admin_externalpage('oidcserver', $label, $manageurl, 'moodle/site:config');
    $ADMIN->add('localplugins', $adminexternal);

    if (local_oidcserver_supports_feature('emulate/community') == 'pro') {
        include_once($CFG->dirroot.'/local/oidcserver/pro/prolib.php');
        $promanager = local_oidcserver\pro_manager::instance();
        $promanager->add_settings($ADMIN, $settings);
    } else {
        $label = get_string('plugindist', 'local_oidcserver');
        $desc = get_string('plugindist_desc', 'local_oidcserver');
        $settings->add(new admin_setting_heading('plugindisthdr', $label, $desc));
    }

}