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

namespace local_oidcserver\controllers;

use moodle_url;

require_once($CFG->dirroot.'/local/oidcserver/lib.php');
require_once($CFG->dirroot.'/local/oidcserver/classes/controller.php');

class scopes extends controller {

    public function receive($cmd, $data = []) {
        parent::receive($cmd, $data);

        // Receive explicit commands.
        switch ($cmd) {
            case "delete": {
                $this->data->scopeid = required_param('id', PARAM_INT);
            }
        }
    }

    public function process($cmd) {
        global $DB;

        parent::process($cmd);
        // Process explicit commands.

        switch ($cmd) {
            case 'add' : {
                $DB->insert_record('local_oidcserver_scope', $this->data);
                return new moodle_url('/local/oidcserver/scopes.php');
            }

            case 'update' : {
                $DB->update_record('local_oidcserver_scope', $this->data);
                return new moodle_url('/local/oidcserver/scopes.php');
            }

            case 'delete' : {
                $DB->delete_records('local_oidcserver_scope', ['id' => $this->data->scopeid]);
            }
        }

    }

}