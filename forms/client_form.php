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

class oidcserver_client_form extends moodleform {

    function definition() {
        
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $group1 = [];
        $group1[] = $mform->createElement('text', 'identifier', get_string('identifier', 'local_oidcserver'));
        $mform->setType('identifier', PARAM_TEXT);
        $mform->disabledIf('identifier', 'generateidentifier', 'checked');

        $group1[] = $mform->createElement('checkbox', 'generateidentifier', '');
        $mform->setType('generateidentifier', PARAM_BOOL);
        $mform->addGroup($group1, 'idgroup', get_string('identifier', 'local_oidcserver'), [get_string('generate', 'local_oidcserver')], false);

        $group2 = [];

        $group2[] = $mform->createElement('text', 'secret', get_string('secret', 'local_oidcserver'));
        $mform->setType('secret', PARAM_TEXT);
        $mform->disabledIf('secret', 'generatesecret', 'checked');

        $group2[] = $mform->createElement('checkbox', 'generatesecret', '');
        $mform->setType('generatesecret', PARAM_BOOL);
        $mform->addGroup($group2, 'secretgroup', get_string('secret', 'local_oidcserver'), [get_string('generate', 'local_oidcserver')], false);

        $mform->addElement('text', 'name', get_string('name'), "size=40 maxlength=40");
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required');

        $mform->addElement('text', 'redirecturi', get_string('redirecturi', 'local_oidcserver'), "size=80 maxlength=255");
        $mform->setType('redirecturi', PARAM_TEXT);
        $mform->addHelpButton('redirecturi', 'redirecturi', 'local_oidcserver');
        $mform->addRule('redirecturi', null, 'required');

        $mform->addElement('text', 'altredirecturi', get_string('altredirecturis', 'local_oidcserver'), "size=80 maxlength=2048");
        $mform->setType('altredirecturi', PARAM_TEXT);
        $mform->addHelpButton('altredirecturi', 'altredirecturi', 'local_oidcserver');

        $mform->addElement('text', 'singlelogouturi', get_string('singlelogouturi', 'local_oidcserver'), "size=80 maxlength=255");
        $mform->addHelpButton('singlelogouturi', 'singlelogouturi', 'local_oidcserver');
        $mform->setType('singlelogouturi', PARAM_TEXT);

        $mform->addElement('textarea', 'userallow', get_string('userallow', 'local_oidcserver'));
        $mform->addHelpButton('userallow', 'userallow', 'local_oidcserver');
        $mform->setType('userallow', PARAM_TEXT);

        $mform->addElement('textarea', 'userdeny', get_string('userdeny', 'local_oidcserver'));
        $mform->addHelpButton('userdeny', 'userdeny', 'local_oidcserver');
        $mform->setType('userdeny', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'isconfidential', get_string('isconfidential', 'local_oidcserver'));
        $mform->setType('isconfidential', PARAM_BOOL);

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