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
 * @package    local_my
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_local_oidcserver_upgrade($oldversion = 0) {
	global $DB;
    $result = true;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025061101) {
        // Define table.
        // Define table to be created.
        $table = new xmldb_table('local_oidcserver_client');

        $field = new xmldb_field('altredirecturis');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, 0, 'redirecturi');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025061101, 'local', 'oidcserver');
    }

    return $result;
}
