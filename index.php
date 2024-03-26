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

$url = new moodle_url('/local/oidcserver/index.php');
$PAGE->set_url($url);

$context = context_system::instance();
$PAGE->set_context($context);

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_pagelayout('admin');
$renderer = $PAGE->get_renderer('local_oidcserver');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('oidcadmin', 'local_oidcserver'));

$menu = [
    (object) [
        'url' => new moodle_url('/local/oidcserver/clients.php'),
        'label' => get_string('clients', 'local_oidcserver')
    ],
    (object) [
        'url' => new moodle_url('/local/oidcserver/scopes.php'),
        'label' => get_string('scopes', 'local_oidcserver')
    ],
    (object) [
        'url' => new moodle_url('/local/oidcserver/authcodes.php'),
        'label' => get_string('authcodes', 'local_oidcserver')
    ],
];

echo $renderer->menu($menu);

echo $OUTPUT->footer();
