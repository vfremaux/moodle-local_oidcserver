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

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/lib/formslib.php');

class consent_form extends moodleform {

    public function definition() {
        $mform = $this->_form;

        $scopes = $this->_customdata['scopes'];

        $mform->addElement('static', 'consentdesc', '', get_string('consenthelp', 'local_oidcserver'));

        foreach ($scopes as $scope) {
            $mform->addElement('static', 'scope'.$scope->get_id(), $scope->getIdentifier(), $scope->get_description());
        }

        $additional = ['phone1', 'idnumber', 'username', 'middlename', 'alternatename', 'institution', 'department', 'address', 'city', 'timezone'];
        foreach ($additional as $fieldkey) {
            if (!empty($USER->$fieldkey)) {
                $mform->addElement('checkbox', 'allow_'.$fieldkey, get_string($fieldkey));
            }
        }

        $mform->addElement('static', 'consentdesctail', '', get_string('consenthelptail', 'local_oidcserver'));

        $this->add_action_buttons(true, get_string('iconsent', 'local_oidcserver'));
    }

} 