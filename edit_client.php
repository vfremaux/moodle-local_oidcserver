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
 * This is the starting point of oidcserver backoffice.
 *
 * @package    local
 * @subpackage oidcserver
 * @copyright  2012 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/local/oidcserver/forms/client_form.php');
require_once($CFG->dirroot.'/local/oidcserver/clients.controller.php');

$id = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/local/oidcserver/edit_client.php');
$PAGE->set_url($url);
$context = context_system::instance();
$PAGE->set_context($context);

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_pagelayout('admin');

$form = new oidcserver_client_form($url);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/oidcserver/clients.php')); 
}

if ($data = $form->get_data()) {
    $controller = new \local_oidcserver\controllers\client();
    if (empty($data->id)) {
        $controller->receive('add', $data);
        $return = $controller->process('add');
    } else {
        $controller->receive('update', $data);
        $return = $controller->process('update');
    }

    redirect($return);
}

if (!empty($id)) {
    $client = $DB->get_record('local_oidcserver_client', ['id' => $id]);
    $form->set_data($client);
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();
