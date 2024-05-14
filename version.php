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

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2023032603;   // The (date) version of this plugin.
$plugin->requires = 2022112801;   // Requires this Moodle version.
$plugin->component = 'local_oidcserver';
$plugin->release = '4.1.0 (build 2023032603)';
$plugin->maturity = MATURITY_RC;
$plugin->supported = [401, 402];

// Non moodle attributes.
$plugin->codeincrement = '4.1.0000';

