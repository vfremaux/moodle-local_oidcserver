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

require('../../config.php');
require_once($CFG->dirroot.'/local/oidcserver/scopes.controller.php');

$action = optional_param('what', '', PARAM_TEXT);

$url = new moodle_url('/local/oidcserver/scopes.php');
$PAGE->set_url($url);
$context = context_system::instance();
$PAGE->set_context($context);

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_pagelayout('admin');

$renderer = $PAGE->get_renderer('local_oidcserver');

if (!empty($action)) {
    $controller = new \local_oidcserver\controllers\scope();
    $controller->receive($action);
    $return = $controller->process($action);
    if ($return) {
        redirect($return);
    }
}

$scopes = $DB->get_records('local_oidcserver_scope', []);

echo $OUTPUT->header();

echo $renderer->scopes($scopes);

echo $renderer->addscopelink();

echo $OUTPUT->footer();