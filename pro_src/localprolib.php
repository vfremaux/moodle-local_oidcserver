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
 * @author      Valery Fremaux <valery.fremaux@gmail.com>, Florence Labord <info@expertweb.fr>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (ActiveProLearn.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_shop;

defined('MOODLE_INTERNAL') || die();

final class local_pro_manager {

    private static $component = 'local_oidcserver';
    private static $componentpath = 'local/oidcserver';

    /**
     * this adds additional settings to the component settings (generic part of the prolib system).
     * @param objectref &$admin
     * @param objectref &$settings
     */
    public static function add_settings(&$admin, &$settings) {
        global $CFG, $PAGE;

        if (local_oidcserver_supports_feature('extended/singlelogout')) {
            $key = 'local_oidcserver/enablesinglelogout';
            $label = get_string('configenablesinglelogout', 'local_oidcserver');
            $desc = get_string('configenablesinglelogout_desc', 'local_oidcserver');
            $settings->add(new admin_setting_configcheckbox($key, $label, $desc, true));
        }

        if (local_oidcserver_supports_feature('extended/userfiltering')) {
            $key = 'local_oidcserver/enableuserfiltering';
            $label = get_string('configenableuserfiltering', 'local_oidcserver');
            $desc = get_string('configenableuserfiltering_desc', 'local_oidcserver');
            $settings->add(new admin_setting_configcheckbox($key, $label, $desc, true));
        }
    }

    public function check_user($user) {
        
    }

}