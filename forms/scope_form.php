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
 * @package    local_oidcserver
 * @category   local
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class oidcserver_scope_form extends moodleform {

    function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'identifier', get_string('identifier', 'local_oidcserver'), "size=40 maxlength=40");
        $mform->setType('identifier', PARAM_TEXT);
        $mform->addRule('identifier', null, 'required');

        $mform->addElement('text', 'description', get_string('name'), "size=80 maxlength=255");
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', null, 'required');

        $this->add_action_buttons();

    }

    function validation($data, $files = []) {
        global $DB;

        $errors = [];

        if (!empty($data->id)) {
            $params = [$data->id, $data->identifier];
            if ($DB->get_record_select('local_oidcserver_client', ' id != ? and identifier = ? ', $params)) {
                $error['identifier'] = "This identifier is used";
            }

            $params = [$data->id, $data->name];
            if ($DB->get_record_select('local_oidcserver_client', ' id != ? and name = ? ', $params)) {
                $error['identifier'] = "This name is used";
            }
        }
    }

}