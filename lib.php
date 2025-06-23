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

define('LOCAL_OIDCS_TRACE_ERRORS', 1); // Errors should be always traced when trace is on.
define('LOCAL_OIDCS_TRACE_NOTICE', 3); // Notices are important notices in normal execution.
define('LOCAL_OIDCS_TRACE_DEBUG', 5); // Debug are debug time notices that should be burried in debug_fine level when debug is ok.
define('LOCAL_OIDCS_TRACE_DATA', 8); // Data level is when requiring to see data structures content.
define('LOCAL_OIDCS_TRACE_DEBUG_FINE', 10); // Debug fine are control points we want to keep when code is refactored and debug needs to be reactivated.

/**
 * @package     local_oidcserver
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This is part of the dual release distribution system.
 * Tells wether a feature is supported or not. Gives back the
 * implementation path where to fetch resources.
 * @param string $feature a feature key to be tested.
 */
function local_oidcserver_supports_feature($feature = null) {
    global $CFG;
    static $supports;

    if (!during_initial_install()) {
        $config = get_config('local_oidcserver');
    }

    if (!isset($supports)) {
        $supports = [
            'pro' => [
                'clients' => ['unlimited'],
                'extended' => ['singlelogout', 'userfiltering'],
                'keys' => ['customsize']
            ],
            'community' => [
                'clients' => ['limited'],
                'keys' => ['fixedsize']
            ],
        ];
    }

    // Check existance of the 'pro' dir in plugin.
    if (is_dir(__DIR__.'/pro')) {
        if ($feature == 'emulate/community') {
            return 'pro';
        }
        if (empty($config->emulatecommunity)) {
            $versionkey = 'pro';
        } else {
            $versionkey = 'community';
        }
    } else {
        $versionkey = 'community';
    }

    if (empty($feature)) {
        // Just return version.
        return $versionkey;
    }

    list($feat, $subfeat) = explode('/', $feature);

    if (!array_key_exists($feat, $supports[$versionkey])) {
        return false;
    }

    if (!in_array($subfeat, $supports[$versionkey][$feat])) {
        return false;
    }

    return $versionkey;
}

/**
 * Plugs before header are sent.
 * @see $OUTPUT->header();
 */
function local_oidcserver_before_http_headers() {
    $config = get_config('local_oidcserver');
    if (!empty($config->forceopeningcors)) {
        header('Access-Control-Allow-Origin: *');
    }
}

/**
 * A wrapper to APL debug. Do not use trace constants here because they may be not installed.
 * @param string $msg
 * @param int $level
 * @param string $label
 * @param int $backtracelevel
 */
function local_oidcserver_debug_trace($msg, $level = LOCAL_OIDCS_TRACE_NOTICE, $label = '', $backtracelevel = 1) {
    if (function_exists('debug_trace')) {
        debug_trace($msg, $level, $label, $backtracelevel + 1);
    }
}